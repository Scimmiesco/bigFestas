public function definition(): array
{
    return [
        'nome' => $this->faker->word() . ' Luxo',
        'tipo' => $this->faker->randomElement([\App\Enums\ItemType::Cadeira, \App\Enums\ItemType::Mesa]),
        'status' => \App\Enums\ItemStatus::Disponivel,
        'team_id' => 1, // Assumindo que seu primeiro time tem ID 1
    ];
}
