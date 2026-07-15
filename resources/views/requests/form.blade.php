@extends('layouts.app')
@php
 $editing=filled($requestItem); $field=fn($n,$d=null)=>old($n,$requestItem?->{$n}??$d);
 $ownerType=$field('owner_type','yuridik'); $existingFiles=$editing?$requestItem->files->keyBy('type'):collect();
 $fileLabels=['warning_letter_file'=>'Огоҳлантириш хати','lease_document_file'=>'Ижарага олиш ҳужжати'];
@endphp
@section('title',$editing?'Реклама объектини таҳрирлаш':'Янги реклама объекти')
@section('breadcrumb','Реклама объектлари')
@section('content')
<section class="page-title compact-title"><div><h1>{{ $editing?'Реклама объектини таҳрирлаш':'Янги реклама объекти' }}</h1>@if($editing)<p>{{ $requestItem->request_number }}</p>@endif</div></section>
<form class="registry-card form-panel stepped-form" method="POST" enctype="multipart/form-data" action="{{ $editing?route('requests.update',$requestItem):route('requests.store') }}" novalidate>
 @csrf @if($editing) @method('PUT') @endif
 <div class="stepper">
  @foreach(['Эгаси','Манзил','Ўлчам','Харита','Файллар'] as $i=>$label)<button class="step {{ $i===0?'active':'' }}" type="button" data-step-target="{{ $i+1 }}">{{ $i+1 }}. {{ $label }}</button>@endforeach
 </div>
 <section class="form-step-panel active" data-step-panel="1"><div class="form-section"><h2>Эгаси</h2><div class="form-grid two">
  <fieldset class="segmented-field"><legend>Эгаси тури</legend><label><input type="radio" name="owner_type" value="yuridik" @checked($ownerType==='yuridik')> Юридик шахс</label><label><input type="radio" name="owner_type" value="jismoniy" @checked($ownerType==='jismoniy')> Жисмоний шахс</label>@error('owner_type')<span>{{ $message }}</span>@enderror</fieldset>
  <label id="owner-name-label">{{ $ownerType==='jismoniy'?'Ф.И.Ш':'Корхона номи' }}<input name="owner_name" value="{{ $field('owner_name') }}" required>@error('owner_name')<span>{{ $message }}</span>@enderror</label>
  <label><span id="owner-identifier-label">{{ $ownerType==='jismoniy'?'ЖШШИР':'СТИР' }}</span><input id="owner_stir_pinfl" name="owner_stir_pinfl" inputmode="numeric" pattern="\d{{ $ownerType==='jismoniy'?'{14}':'{9}' }}" maxlength="{{ $ownerType==='jismoniy'?14:9 }}" value="{{ $field('owner_stir_pinfl') }}" placeholder="{{ $ownerType==='jismoniy'?'14 хонали ЖШШИР':'9 хонали СТИР' }}" required>@error('owner_stir_pinfl')<span>{{ $message }}</span>@enderror</label>
  <label>Раҳбари<input name="director_name" value="{{ $field('director_name') }}" required>@error('director_name')<span>{{ $message }}</span>@enderror</label>
  <label>Телефони<input id="phone_number" name="phone_number" type="tel" inputmode="numeric" maxlength="19" value="{{ $field('phone_number') }}" placeholder="+998 (NN) NNN-NN-NN" autocomplete="tel" required>@error('phone_number')<span>{{ $message }}</span>@enderror</label>
  <div class="readonly-field"><span>Яратувчи</span><strong>{{ $editing?$requestItem->creator->name:auth()->user()->name }}</strong></div>
 </div></div></section>
 <section class="form-step-panel" data-step-panel="2"><div class="form-section"><h2>Манзил</h2><div class="form-grid two">
  <label>Туман<select name="district_id" id="district_id" class="searchable-select" required><option value="">Танланг</option>@foreach($districts as $v)<option value="{{ $v->id }}" @selected((string)$field('district_id')===(string)$v->id)>{{ $v->name }}</option>@endforeach</select>@error('district_id')<span>{{ $message }}</span>@enderror</label>
  <label>Маҳалла<select name="mahalla_id" id="mahalla_id" class="searchable-select" required><option value="">Танланг</option>@foreach($mahallas as $v)<option value="{{ $v->id }}" data-district="{{ $v->district_id }}" @selected((string)$field('mahalla_id')===(string)$v->id)>{{ $v->name }}</option>@endforeach</select>@error('mahalla_id')<span>{{ $message }}</span>@enderror</label>
  <label>Кўча тури<select name="street_type" id="street_type" required>@foreach($streetTypes as $k=>$v)<option value="{{ $k }}" @selected($field('street_type','kocha')===$k)>{{ $v }}</option>@endforeach</select>@error('street_type')<span>{{ $message }}</span>@enderror</label>
  <label>Кўча номи<div class="inline-field"><select name="street_id" id="street_id" class="searchable-select" required><option value="">Танланг</option>@foreach($streets as $v)<option value="{{ $v->id }}" data-district="{{ $v->district_id }}" data-mahalla="{{ $v->mahalla_id }}" @selected((string)$field('street_id')===(string)$v->id)>{{ $v->name }}</option>@endforeach</select>@can('create',App\Models\Street::class)<button class="icon-button" type="button" id="add-street" aria-label="Янги кўча қўшиш">+</button>@endcan</div>@error('street_id')<span>{{ $message }}</span>@enderror</label>
  <label>Уй рақами<input name="house_number" value="{{ $field('house_number') }}" required>@error('house_number')<span>{{ $message }}</span>@enderror</label>
  @can('create',App\Models\Street::class)<label class="wide hidden" id="new-street-wrap">Янги кўча номи<input id="new_street_name"><small>Номни киритиб, + тугмасини яна босинг.</small></label>@endcan
 </div></div></section>
 <section class="form-step-panel" data-step-panel="3"><div class="form-section"><h2>Ўлчам ва конструкция</h2><div class="form-grid two">
  <label class="wide">Реклама конструкцияси тури
   <select id="advertising_type" name="advertising_type" required><option value="">Танланг</option>@foreach($advertisingGroups as $group=>$types)<optgroup label="{{ $group }}">@foreach($types as $type)<option value="{{ $type }}" data-description="{{ $advertisingDescriptions[$type] }}" @selected($field('advertising_type')===$type)>{{ $advertisingTypes[$type] }}</option>@endforeach</optgroup>@endforeach</select>
   <small id="advertising-type-description" class="field-help"></small>@error('advertising_type')<span>{{ $message }}</span>@enderror
  </label>
  <label>Узунлик (м)<input id="area_length" name="area_length" type="number" step="0.01" min="0.01" value="{{ $field('area_length') }}" required>@error('area_length')<span>{{ $message }}</span>@enderror</label>
  <label>Кенглик (м)<input id="area_width" name="area_width" type="number" step="0.01" min="0.01" value="{{ $field('area_width') }}" required>@error('area_width')<span>{{ $message }}</span>@enderror</label>
  <label>Майдони (м²)<input id="total_area" name="total_area" type="number" step="0.01" value="{{ $field('total_area') }}" readonly required>@error('total_area')<span>{{ $message }}</span>@enderror</label>
  <label>Неча томони реклама объекти<select name="advertising_sides" required>@for($i=1;$i<=8;$i++)<option value="{{ $i }}" @selected((int)$field('advertising_sides',1)===$i)>{{ $i }}</option>@endfor</select>@error('advertising_sides')<span>{{ $message }}</span>@enderror</label>
  <fieldset class="segmented-field"><legend>Паспорт мавжудми?</legend><label><input type="radio" name="has_passport" value="1" @checked((bool)$field('has_passport'))> Ҳа</label><label><input type="radio" name="has_passport" value="0" @checked(!$field('has_passport'))> Йўқ</label></fieldset>
  <label id="passport-details-wrap">Паспорт маълумотлари<textarea name="passport_details" maxlength="2000">{{ $field('passport_details') }}</textarea>@error('passport_details')<span>{{ $message }}</span>@enderror</label>
  <label>Шартномаси<input name="contract_number" value="{{ $field('contract_number') }}">@error('contract_number')<span>{{ $message }}</span>@enderror</label>
  <label>Шартнома суммаси<input name="contract_amount" type="number" step="0.01" min="0" inputmode="decimal" value="{{ $field('contract_amount') }}" placeholder="0.00">@error('contract_amount')<span>{{ $message }}</span>@enderror</label>
 </div></div></section>
 <section class="form-step-panel" data-step-panel="4"><div class="form-section"><h2>Харита</h2><p class="map-help">Харитадан битта локацияни белгиланг. Маркерни суриб аниқлаштириш мумкин.</p><div class="map-toolbar"><button type="button" class="map-tool locate" id="locate-single-position">Жорий жойлашувимни аниқлаш</button><span id="location-status" class="field-help"></span></div><div id="location-map" class="leaflet-map" data-lat="{{ $field('latitude') }}" data-lng="{{ $field('longitude') }}"></div><input type="hidden" id="latitude" name="latitude" value="{{ $field('latitude') }}"><input type="hidden" id="longitude" name="longitude" value="{{ $field('longitude') }}">@error('latitude')<p class="field-error">{{ $message }}</p>@enderror @error('longitude')<p class="field-error">{{ $message }}</p>@enderror</div></section>
 <section class="form-step-panel" data-step-panel="5"><div class="form-section"><h2>Файллар</h2><div class="form-grid two">
  @if($editing&&$requestItem->images->isNotEmpty())<div class="wide readonly-media-grid">@foreach($requestItem->images as $image)<a class="readonly-media-card" href="{{ Storage::url($image->path) }}" target="_blank"><img src="{{ Storage::url($image->path) }}"><span>{{ $image->original_name }}</span></a>@endforeach</div>@endif
  <label class="wide">Расм (битта)<input name="images[]" type="file" accept="image/*" {{ $editing?'':'required' }}>@error('images')<span>{{ $message }}</span>@enderror @error('images.*')<span>{{ $message }}</span>@enderror</label>
  @foreach($fileLabels as $type=>$label)<label>{{ $label }} юклаш @if($existingFiles->get($type))<small><a href="{{ Storage::url($existingFiles->get($type)->path) }}" target="_blank">Мавжуд файл</a></small>@endif<input name="{{ $type }}" type="file" accept=".pdf,image/*">@error($type)<span>{{ $message }}</span>@enderror</label>@endforeach
 </div></div></section>
 <div class="form-actions sticky-actions"><a class="ghost-button" href="{{ route('requests.index') }}">Бекор қилиш</a><button class="secondary-button" type="button" data-step-prev>Орқага</button><button class="secondary-button" type="button" data-step-next>Кейингиси</button><button class="primary-button" type="submit">Сақлаш</button></div>
</form>
@endsection
