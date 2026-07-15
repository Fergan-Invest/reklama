<?php

namespace App\Http\Requests;

use App\Models\RegistryRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RegistryRequestFormRequest extends FormRequest
{
    public function authorize(): bool
    {
        $item = $this->route('registryRequest');
        return $item ? $this->user()->can('update', $item) : $this->user()->can('create', RegistryRequest::class);
    }

    public function rules(): array
    {
        $item = $this->route('registryRequest');
        $districtId = (int) $this->input('district_id');
        $mahallaId = (int) $this->input('mahalla_id');

        return [
            'owner_type' => ['required', Rule::in(['jismoniy', 'yuridik'])],
            'owner_name' => ['required', 'string', 'max:255'],
            'owner_stir_pinfl' => ['required', $this->input('owner_type') === 'jismoniy' ? 'digits:14' : 'digits:9'],
            'director_name' => ['required', 'string', 'max:255'],
            'phone_number' => ['required', 'regex:/^\+998 \(\d{2}\) \d{3}-\d{2}-\d{2}$/'],
            'district_id' => ['required', 'exists:districts,id'],
            'mahalla_id' => ['required', Rule::exists('mahallas', 'id')->where('district_id', $districtId)],
            'street_id' => ['required', Rule::exists('streets', 'id')->where('district_id', $districtId)->where('mahalla_id', $mahallaId)],
            'street_type' => ['required', Rule::in(array_keys(RegistryRequest::STREET_TYPES))],
            'house_number' => ['required', 'string', 'max:80'],
            'advertising_type' => ['required', Rule::in(array_keys(RegistryRequest::ADVERTISING_TYPES))],
            'area_length' => ['required', 'numeric', 'min:0.01'],
            'area_width' => ['required', 'numeric', 'min:0.01'],
            'total_area' => ['required', 'numeric', 'min:0.01'],
            'advertising_sides' => ['required', 'integer', 'min:1', 'max:8'],
            'has_passport' => ['required', 'boolean'],
            'passport_details' => ['nullable', Rule::requiredIf($this->boolean('has_passport')), 'string', 'max:2000'],
            'contract_number' => ['nullable', 'string', 'max:255'],
            'contract_amount' => ['nullable', 'numeric', 'min:0'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'images' => [$item ? 'nullable' : 'required', 'array', 'max:1'],
            'images.*' => ['image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'warning_letter_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
            'lease_document_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->user()?->isTuman() && (int) $this->input('district_id') !== (int) $this->user()->district_id) {
                $validator->errors()->add('district_id', 'Туман фойдаланувчиси фақат ўз ҳудуди бўйича маълумот киритади.');
            }

            $item = $this->route('registryRequest');
            if ($item && ! $this->hasFile('images') && ! $item->images()->exists()) {
                $validator->errors()->add('images', 'Битта расм юкланиши шарт.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'required' => ':attribute майдони мажбурий.', 'required_if' => ':attribute майдони мажбурий.',
            'exists' => 'Танланган :attribute топилмади.', 'in' => 'Танланган :attribute нотўғри.',
            'numeric' => ':attribute рақам бўлиши керак.', 'integer' => ':attribute бутун сон бўлиши керак.',
            'regex' => ':attribute формати нотўғри.', 'digits' => ':attribute :digits хонали бўлиши керак.',
            'images.required' => 'Битта расм юкланг.', 'images.max' => 'Фақат битта расм юклаш мумкин.',
            'images.*.image' => 'Юкланган файл расм бўлиши керак.',
        ];
    }

    public function attributes(): array
    {
        return [
            'owner_type' => 'Эгаси тури', 'owner_name' => 'Эгаси номи',
            'owner_stir_pinfl' => $this->input('owner_type') === 'jismoniy' ? 'ЖШШИР' : 'СТИР',
            'director_name' => 'Раҳбари', 'phone_number' => 'Телефони', 'district_id' => 'Туман',
            'mahalla_id' => 'Маҳалла', 'street_id' => 'Кўча номи', 'street_type' => 'Кўча тури',
            'house_number' => 'Уй рақами', 'advertising_type' => 'Реклама конструкцияси тури',
            'area_length' => 'Узунлик', 'area_width' => 'Кенглик', 'total_area' => 'Майдони',
            'advertising_sides' => 'Реклама томонлари сони', 'has_passport' => 'Паспорт мавжудлиги',
            'passport_details' => 'Паспорт маълумотлари', 'contract_number' => 'Шартнома',
            'contract_amount' => 'Шартнома суммаси', 'latitude' => 'Локация кенглиги', 'longitude' => 'Локация узунлиги',
        ];
    }

    protected function prepareForValidation(): void
    {
        $area = is_numeric($this->input('area_length')) && is_numeric($this->input('area_width'))
            ? round((float) $this->input('area_length') * (float) $this->input('area_width'), 2) : null;
        $this->merge(['total_area' => $area ?? $this->input('total_area'), 'has_passport' => $this->boolean('has_passport')]);
    }
}
