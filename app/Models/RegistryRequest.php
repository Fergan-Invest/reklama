<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class RegistryRequest extends Model
{
    use SoftDeletes;

    public const STATUSES = ['draft', 'submitted', 'in_review', 'approved', 'rejected'];
    public const STREET_TYPES = [
        'kocha' => 'Ko‘cha',
        'gostronomik' => 'Шоҳкўча',
        'turizm' => 'Тор кўча',
    ];
    public const ADVERTISING_TYPES = [
        'billboard' => 'Билборд (реклама шити)',
        'superposter' => 'Суперпостер ёки суперсайт',
        'city_format' => 'Сити-формат (лайтбокс)',
        'stela_pylon' => 'Стела ёки пилон',
        'scroller' => 'Скроллер (динамик шит)',
        'brandmauer' => 'Брандмауэр (деворий панно)',
        'roof' => 'Том қисмидаги қурилма',
        'bracket' => 'Кронштейн (панель-кронштейн)',
        'showcase_awning' => 'Витрина ёки маркиза',
        'led' => 'LED экран',
        'media_facade' => 'Медиафасад',
        'ticker' => 'Югурувчи сатр (электрон табло)',
        'bus_stop' => 'Йўловчи бекатидаги реклама',
        'bridge' => 'Йўл ўтказгич ёки кўприкдаги конструкция',
        'poster_column' => 'Афиша устуни ёки тумба',
    ];
    public const ADVERTISING_GROUPS = [
        'Ер майдонларида ўрнатиладиган алоҳида турувчи конструкциялар' => [
            'billboard', 'superposter', 'city_format', 'stela_pylon', 'scroller',
        ],
        'Бино ва иншоотларда жойлаштириладиган конструкциялар' => [
            'brandmauer', 'roof', 'bracket', 'showcase_awning',
        ],
        'Замонавий электрон ва рақамли воситалар' => [
            'led', 'media_facade', 'ticker',
        ],
        'Шаҳар ва йўл инфратузилмаси объектларидаги рекламалар' => [
            'bus_stop', 'bridge', 'poster_column',
        ],
    ];
    public const ADVERTISING_DESCRIPTIONS = [
        'billboard' => 'Одатда 3×6 метр ёки ундан катта, алоҳида пойдевор устига ўрнатиладиган икки ёки уч томонлама реклама шити.',
        'superposter' => 'Магистрал йўллар бўйида ўрнатиладиган жуда йирик, масалан 5×15 ёки 4×12 метрли баланд устунли конструкция.',
        'city_format' => 'Пиёдалар йўлаклари ва бекатлар яқинидаги, ичидан ёритиладиган, одатда 1.2×1.8 метрли реклама қутиси.',
        'stela_pylon' => 'АЙШ ва савдо марказлари кириш қисмида ерга вертикал ўрнатиладиган яхлит конструктив объект.',
        'scroller' => 'Реклама плакатлари белгиланган вақт оралиғида автоматик айланиб алмашадиган қурилма.',
        'brandmauer' => 'Бино фасадига бутунлай ёки қисман маҳкамланадиган йирик баннер ёки конструкция.',
        'roof' => 'Бино томида ўрнатиладиган, ёритиладиган йирик ҳарф, логотип ёки шит.',
        'bracket' => 'Девор ёки ёритиш устунига перпендикуляр маҳкамланадиган икки томонлама кичик реклама тахтаси.',
        'showcase_awning' => 'Витрина ичи/ташқарисига ёки соябонга жойлаштирилган реклама материали.',
        'led' => 'Видеоролик ва динамик тасвирларни намойиш қилувчи ташқи электрон экран.',
        'media_facade' => 'Бино ташқи қисмига интеграция қилинган ва архитектура билан яхлит электрон тизим.',
        'ticker' => 'Матнли маълумотни ҳаракатланувчи шаклда кўрсатувчи электрон табло.',
        'bus_stop' => 'Жамоат транспорти бекатининг ён ёки орқа деворидаги реклама майдони.',
        'bridge' => 'Кўприк ёки йўл ўтказгич ён томонига маҳкамланган узун реклама конструкцияси.',
        'poster_column' => 'Тадбир, концерт ва спектакль эълонлари учун цилиндр ёки кўп қиррали махсус устун.',
    ];

    protected $fillable = [
        'request_number', 'status', 'created_by', 'updated_by', 'building_cadastr_number',
        'hokimyatga_biriktirilgan_kadastr_raqami', 'owner_type', 'owner_stir_pinfl', 'owner_name',
        'district_id', 'mahalla_id', 'street_id', 'house_number', 'street_type', 'director_name',
        'phone_number', 'advertising_type', 'area_length', 'area_width', 'calculated_land_area', 'total_area',
        'advertising_sides', 'has_passport', 'passport_details', 'contract_number', 'contract_amount',
        'total_area_manual',
        'building_facade_length', 'summer_terrace_sides', 'distance_to_roadway',
        'distance_to_sidewalk', 'usage_purpose', 'activity_type', 'terrace_buildings_available',
        'terrace_buildings_permanent', 'has_permit', 'has_tenant', 'tenant_stir_pinfl',
        'tenant_name', 'tenant_activity_type', 'adjacent_activity_type', 'adjacent_activity_land',
        'adjacent_facilities', 'additional_info', 'latitude', 'longitude', 'polygon_coordinates',
    ];

    protected $casts = [
        'terrace_buildings_available' => 'boolean',
        'terrace_buildings_permanent' => 'boolean',
        'has_permit' => 'boolean',
        'has_tenant' => 'boolean',
        'total_area_manual' => 'boolean',
        'has_passport' => 'boolean',
        'adjacent_facilities' => 'array',
        'polygon_coordinates' => 'array',
    ];

    protected static function booted(): void
    {
        static::deleting(function (RegistryRequest $request) {
            if ($request->isForceDeleting()) {
                Storage::disk('public')->deleteDirectory("requests/{$request->id}");
            }
        });
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function district()
    {
        return $this->belongsTo(District::class);
    }

    public function mahalla()
    {
        return $this->belongsTo(Mahalla::class);
    }

    public function street()
    {
        return $this->belongsTo(Street::class);
    }

    public function images()
    {
        return $this->hasMany(RequestImage::class);
    }

    public function files()
    {
        return $this->hasMany(RequestFile::class);
    }

    public function audits()
    {
        return $this->morphMany(AuditLog::class, 'auditable')->latest();
    }
}
