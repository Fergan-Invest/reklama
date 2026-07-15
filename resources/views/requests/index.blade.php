@extends('layouts.app')
@section('title','Реклама объектлари')
@section('breadcrumb','Реклама объектлари')
@section('topbar-actions')
 <a class="secondary-button topbar-export-button" href="{{ route('requests.monitoring',request()->query()) }}">Мониторинг</a>
 <a class="secondary-button topbar-export-button" href="{{ route('requests.export',request()->query()) }}">Excel</a>
@endsection
@php($fileLabels=['warning_letter_file'=>'Огоҳлантириш','lease_document_file'=>'Ижара'])
@section('content')
<section class="page-title compact-title"><div><h1>Реклама объектлари</h1></div>@can('create',App\Models\RegistryRequest::class)<a class="primary-button" href="{{ route('requests.create') }}">Объект қўшиш</a>@endcan</section>
<form class="panel filters soft-panel" method="GET">
 <input name="q" value="{{ request('q') }}" placeholder="СТИР, ЖШШИР, телефон ёки эгаси бўйича қидириш">
 <select name="advertising_type"><option value="">Барча конструкциялар</option>@foreach($advertisingGroups as $group=>$types)<optgroup label="{{ $group }}">@foreach($types as $type)<option value="{{ $type }}" @selected(request('advertising_type')===$type)>{{ $advertisingTypes[$type] }}</option>@endforeach</optgroup>@endforeach</select>
 <select name="district_id"><option value="">Барча туманлар</option>@foreach($districts as $district)<option value="{{ $district->id }}" @selected((string)request('district_id')===(string)$district->id)>{{ $district->name }}</option>@endforeach</select>
 <select name="mahalla_id" class="searchable-select"><option value="">Барча маҳаллалар</option>@foreach($mahallas as $mahalla)<option value="{{ $mahalla->id }}" @selected((string)request('mahalla_id')===(string)$mahalla->id)>{{ $mahalla->name }}</option>@endforeach</select>
 <input name="date_from" type="date" value="{{ request('date_from') }}"><input name="date_to" type="date" value="{{ request('date_to') }}">
 <select name="per_page">@foreach($perPageOptions as $option)<option value="{{ $option }}" @selected($perPage===$option)>{{ $option }} қатор</option>@endforeach</select>
 <button class="secondary-button" type="submit">Фильтрлаш</button>
</form>
@if($requests->isEmpty())
 <section class="empty-state-card"><h2>Маълумот йўқ</h2><p>Ҳали бирорта реклама объекти киритилмаган.</p>@can('create',App\Models\RegistryRequest::class)<a class="primary-button empty-action" href="{{ route('requests.create') }}">Объект қўшиш</a>@endcan</section>
@else
 <section class="panel table-panel registry-card"><div class="table-wrap"><table class="registry-table"><thead><tr><th>Т/р</th><th>Эгаси</th><th>Манзил</th><th>Конструкция</th><th>Ўлчам</th><th>Файллар</th><th>Сана</th><th></th></tr></thead><tbody>
 @foreach($requests as $item) @php($uploaded=$item->files->pluck('type')->flip())
 <tr class="clickable-row" data-href="{{ route('requests.show',$item) }}" onclick="window.location=this.dataset.href"><td><span class="row-number">{{ $requests->firstItem()+$loop->index }}</span></td><td>{{ $item->owner_name }}<small>{{ $item->owner_stir_pinfl }}</small></td><td>{{ $item->district->name }}<small>{{ $item->mahalla->name }}, {{ $item->street->name }}, {{ $item->house_number }}</small></td><td>{{ $advertisingTypes[$item->advertising_type]??'-' }}</td><td>{{ $item->area_length }} × {{ $item->area_width }} м<small>{{ $item->total_area }} м²</small></td><td><div class="file-status-list">@foreach($fileLabels as $type=>$label)<span class="file-status-chip {{ $uploaded->has($type)?'uploaded':'missing' }}">{{ $label }} <b>{{ $uploaded->has($type)?'✓':'-' }}</b></span>@endforeach</div></td><td>{{ $item->created_at->format('d.m.Y H:i') }}</td><td><a class="row-link row-action-button" href="{{ route('requests.show',$item) }}" onclick="event.stopPropagation()">Кириш</a></td></tr>
 @endforeach
 </tbody></table></div><div class="pagination-bar"><p>{{ $requests->firstItem() }}–{{ $requests->lastItem() }} / {{ $requests->total() }} та ёзув</p>{{ $requests->onEachSide(2)->links() }}</div></section>
@endif
@endsection
