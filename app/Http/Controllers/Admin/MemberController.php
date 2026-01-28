<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Membership;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Inertia\Inertia;

class MemberController extends Controller
{
    /**
     * メンバー一覧
     */
    public function index()
    {
        $user = Auth::user();
        $companyId = $user->company_id;

        // 同じ会社のメンバーシップを取得
        $members = Membership::where('company_id', $companyId)
            ->where('status', 'active')
            ->join('users', 'memberships.user_id', '=', 'users.id')
            ->select('memberships.*', 'users.name', 'users.email')
            ->orderBy('users.name')
            ->get();

        return Inertia::render('Admin/Members/Index', [
            'auth' => ['user' => $user],
            'members' => $members,
            'roles' => $this->getRoles(),
        ]);
    }

    /**
     * 作成フォーム
     */
    public function create()
    {
        return Inertia::render('Admin/Members/Create', [
            'auth' => ['user' => Auth::user()],
            'roles' => $this->getRoles(),
        ]);
    }

    /**
     * 新規メンバー作成
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
            'role' => ['required', 'string', 'in:admin,recruiter,interviewer,viewer'],
        ], [
            'email.unique' => 'このメールアドレスは既に使用されています。',
            'password.confirmed' => 'パスワードが一致しません。',
        ]);

        $adminUser = Auth::user();
        $companyId = $adminUser->company_id;

        DB::transaction(function () use ($validated, $companyId) {
            // ユーザー作成
            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'company_id' => $companyId,
                'role' => $validated['role'],
            ]);

            // メンバーシップ作成
            Membership::create([
                'id' => (string) Str::uuid(),
                'user_id' => $user->id,
                'company_id' => $companyId,
                'role' => $validated['role'],
                'status' => 'active',
            ]);
        });

        return redirect()->route('admin.members.index')
            ->with('status', 'member-created');
    }

    /**
     * 編集フォーム
     */
    public function edit(string $id)
    {
        $adminUser = Auth::user();
        $companyId = $adminUser->company_id;

        $membership = Membership::where('id', $id)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->firstOrFail();

        $user = User::findOrFail($membership->user_id);

        return Inertia::render('Admin/Members/Edit', [
            'auth' => ['user' => $adminUser],
            'member' => [
                'id' => $membership->id,
                'user_id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $membership->role,
            ],
            'roles' => $this->getRoles(),
        ]);
    }

    /**
     * 権限更新
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'role' => ['required', 'string', 'in:admin,recruiter,interviewer,viewer'],
        ]);

        $adminUser = Auth::user();
        $companyId = $adminUser->company_id;

        $membership = Membership::where('id', $id)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->firstOrFail();

        // 自分自身の権限は変更不可
        if ($membership->user_id == $adminUser->id) {
            return back()->withErrors(['role' => '自分自身の権限は変更できません。']);
        }

        DB::transaction(function () use ($membership, $validated) {
            // メンバーシップの権限を更新
            $membership->role = $validated['role'];
            $membership->save();

            // ユーザーテーブルのroleも更新
            User::where('id', $membership->user_id)->update([
                'role' => $validated['role'],
            ]);
        });

        return redirect()->route('admin.members.index')
            ->with('status', 'role-updated');
    }

    /**
     * メンバー削除（revoke）
     */
    public function destroy(string $id)
    {
        $adminUser = Auth::user();
        $companyId = $adminUser->company_id;

        $membership = Membership::where('id', $id)
            ->where('company_id', $companyId)
            ->where('status', 'active')
            ->firstOrFail();

        // 自分自身は削除不可
        if ($membership->user_id == $adminUser->id) {
            return back()->withErrors(['delete' => '自分自身は削除できません。']);
        }

        // statusをrevokedに変更（論理削除）
        $membership->status = 'revoked';
        $membership->save();

        return redirect()->route('admin.members.index')
            ->with('status', 'member-deleted');
    }

    /**
     * 利用可能なロール一覧
     */
    private function getRoles(): array
    {
        return [
            ['value' => 'admin', 'label' => '管理者'],
            ['value' => 'recruiter', 'label' => '採用担当'],
            ['value' => 'interviewer', 'label' => '面接官'],
            ['value' => 'viewer', 'label' => '閲覧者'],
        ];
    }
}
