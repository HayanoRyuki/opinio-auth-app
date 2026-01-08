public function up(): void
{
    Schema::create('clients', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->timestamps();
    });
}

