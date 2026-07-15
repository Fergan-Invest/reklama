<?php

namespace Tests\Feature;

use App\Models\District;
use App\Models\Mahalla;
use App\Models\RegistryRequest;
use App\Models\Street;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class RequestValidationTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_form_contains_every_technical_requirement_and_no_legacy_fields(): void
    {
        [$user] = $this->actor();

        $this->actingAs($user)->get(route('requests.create'))
            ->assertOk()
            ->assertSee('Эгаси тури')->assertSee('Яратувчи')->assertSee('Кўча тури')
            ->assertSee('Билборд')->assertSee('Медиафасад')->assertSee('Афиша устуни')
            ->assertSee('Паспорт мавжудми?')->assertSee('Шартнома суммаси')
            ->assertSee('Расм (битта)')->assertSee('Огоҳлантириш хати')->assertSee('Ижарага олиш ҳужжати')
            ->assertSee('id="location-map"', false)
            ->assertDontSee('Кадастр рақами')->assertDontSee('Туташ ҳудуд')->assertDontSee('Полигон');
    }

    public function test_legal_owner_and_single_location_object_can_be_created(): void
    {
        Storage::fake('public');
        [$user, $district, $mahalla, $street] = $this->actor();
        $payload = $this->payload($district, $mahalla, $street);

        $this->actingAs($user)->post(route('requests.store'), $payload)->assertRedirect();

        $item = RegistryRequest::with(['images', 'files'])->firstOrFail();
        $this->assertSame('billboard', $item->advertising_type);
        $this->assertSame(60.0, (float) $item->total_area);
        $this->assertCount(1, $item->images);
        $this->assertCount(2, $item->files);
        $this->assertSame('Point', $item->polygon_coordinates['type']);
        $this->assertDatabaseCount('audit_logs', 1);
    }

    public function test_owner_identifier_length_depends_on_owner_type(): void
    {
        [$user, $district, $mahalla, $street] = $this->actor();
        $payload = $this->payload($district, $mahalla, $street);
        $payload['owner_stir_pinfl'] = '12345678901234';
        $this->actingAs($user)->post(route('requests.store'), $payload)->assertSessionHasErrors('owner_stir_pinfl');

        $payload['owner_type'] = 'jismoniy';
        $this->actingAs($user)->post(route('requests.store'), $payload)->assertRedirect();
    }

    public function test_phone_format_and_exactly_one_image_are_enforced(): void
    {
        [$user, $district, $mahalla, $street] = $this->actor();
        $payload = $this->payload($district, $mahalla, $street);
        $payload['phone_number'] = '998901234567';
        $payload['images'][] = $this->image('second.png');

        $this->actingAs($user)->post(route('requests.store'), $payload)
            ->assertSessionHasErrors(['phone_number', 'images']);
    }

    public function test_passport_details_are_required_only_when_passport_exists(): void
    {
        [$user, $district, $mahalla, $street] = $this->actor();
        $payload = $this->payload($district, $mahalla, $street);
        $payload['passport_details'] = '';
        $this->actingAs($user)->post(route('requests.store'), $payload)->assertSessionHasErrors('passport_details');

        $payload['has_passport'] = 0;
        $this->actingAs($user)->post(route('requests.store'), $payload)->assertRedirect();
    }

    private function actor(): array
    {
        $district = District::create(['external_id' => 1, 'name' => 'Фарғона шаҳар']);
        $mahalla = Mahalla::create(['district_id' => $district->id, 'name' => 'Ойбек']);
        $street = Street::create(['district_id' => $district->id, 'mahalla_id' => $mahalla->id, 'name' => 'Навоий', 'type' => 'kocha']);
        $user = User::create(['name' => 'Invest', 'email' => 'invest@example.com', 'password' => 'secret', 'role' => 'invest']);
        return [$user, $district, $mahalla, $street];
    }

    private function payload(District $district, Mahalla $mahalla, Street $street): array
    {
        return [
            'owner_type' => 'yuridik', 'owner_name' => 'Реклама МЧЖ', 'owner_stir_pinfl' => '123456789',
            'director_name' => 'Али Валиyev', 'phone_number' => '+998 (90) 123-45-67',
            'district_id' => $district->id, 'mahalla_id' => $mahalla->id, 'street_id' => $street->id,
            'street_type' => 'kocha', 'house_number' => '12', 'advertising_type' => 'billboard',
            'area_length' => 10, 'area_width' => 6, 'total_area' => 1, 'advertising_sides' => 2,
            'has_passport' => 1, 'passport_details' => 'РП-001', 'contract_number' => 'SH-01',
            'contract_amount' => 1500000, 'latitude' => 40.3777, 'longitude' => 71.7978,
            'images' => [$this->image('object.png')],
            'warning_letter_file' => UploadedFile::fake()->create('warning.pdf', 10, 'application/pdf'),
            'lease_document_file' => UploadedFile::fake()->create('lease.pdf', 10, 'application/pdf'),
        ];
    }

    private function image(string $name): UploadedFile
    {
        return UploadedFile::fake()->createWithContent($name, base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAQAAAC1HAwCAAAAC0lEQVR42mP8/x8AAwMCAO+/p9sAAAAASUVORK5CYII='));
    }
}
