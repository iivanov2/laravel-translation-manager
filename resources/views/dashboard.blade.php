@extends('vendor.translation-manager.layout')

@section('body')

@include('vendor.translation-manager.header_section')

<script>
    jQuery(document).ready(function($){

        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                console.log('beforesend');
                settings.data += "&_token=<?php echo csrf_token() ?>";
            }
        });

        $('.form-import').on('ajax:success', function (e, data) {
            $('div.success-import strong.counter').text(data.counter);
            $('div.success-import').slideDown();
            window.location.reload();
        });

        $('.form-find').on('ajax:success', function (e, data) {
            $('div.success-find strong.counter').text(data.counter);
            $('div.success-find').slideDown();
            window.location.reload();
        });

        $('.form-publish-all').on('ajax:success', function (e, data) {
            $('div.success-publish-all').slideDown();
            window.location.reload();
        });








        $('#base-locale').change(function (event) {
            console.log($(this).val());
            $.cookie('base_locale', $(this).val());
        })
        if (typeof $.cookie('base_locale') !== 'undefined') {
            $('#base-locale').val($.cookie('base_locale'));
        }

    })
</script>

<div class="container-fluid">

    <div class="row" style="margin:0px 15px 15px 15px;">
        
        <form id="form_import_groups" class="form-import" method="POST" action="{{action('\Barryvdh\TranslationManager\Controller@postImport')}}" data-remote="true" role="form" style="display: inline;">
            @csrf
            <input id="import_or_update" name="import_or_update" type="hidden" value="0">

            <div class="btn-group">
                <button onclick="$('#import_or_update').submit();" type="button" class="btn btn-success" data-disable-with="Loading..." data-toggle="tooltip" data-placement="top" title="Импортира всички нови стрингове от lang файловете в таблицата за превод. Тези, които вече са били импортирани, не се импортират или ъпдейтват.">
                    Import groups
                </button>
                <button onclick="$('#import_or_update').val(1);$('#import_or_update').submit();" type="button" class="btn btn-success" data-disable-with="Loading..." data-toggle="tooltip" data-placement="top" title="Ъпдейтва импортираните стрингове, ако в lang файловете са били направени промени.">
                    Update groups
                </button>
            </div>
        </form>

        <form class="form-find" method="POST" action="{{action('\Barryvdh\TranslationManager\Controller@postFind')}}" data-remote="true" role="form" style="display: inline;"
                data-confirm="Are you sure you want to scan you app folder? All found translation keys will be added to the database.">
            @csrf
            <button type="submit" class="btn btn-info" data-disable-with="Searching..." data-toggle="tooltip" data-placement="top" 
                    title="It will extract all strings throughout the website and will prepare them for translating.">
                Scan files for strings
            </button>
        </form>

        <form class="form-publish-all" method="POST" action="{{action('\Barryvdh\TranslationManager\Controller@postPublish', '*')}}" data-remote="true" role="form" style="display: inline;"
            data-confirm="Are you sure you want to publish all translations group? This will overwrite existing language files.">
            @csrf
            <button type="submit" class="btn btn-primary" data-disable-with="Publishing..."
                data-toggle="tooltip" data-placement="top" title="Новите преводи трябва да се публикуват изрично, за да станат видими в сайта. Ако не ги публикувате, те ще са видими само в модула за преводи.">
                    Публикуване на преводите
            </button>
        </form>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <form class="form-inline" method="GET" action="{{action('\Barryvdh\TranslationManager\Controller@postListGroup')}}">
                    <label>Изберете група за превод</label>
                    <select  onchange="this.form.submit();" name="group" class="form-control group-select">
                        <option value=""></option>
                        @foreach($groups as $groupKey => $groupValue)
                            <option value="{{$groupKey}}">{{$groupValue}}</option>
                        @endforeach
                    </select>
                </form>
                <hr>
            </div>

            <div class="col-md-6">
                <form class="form-inline" method="POST" action="{{ action('\Barryvdh\TranslationManager\Controller@postSearchString') }}">
                    @csrf
                    <label>Търсене на текст</label>
                    <input type="text" class="form-control" name="string_to_find">
                    <input type="submit" class="btn btn-primary" value="Търси">
                </form>
                <hr>
            </div>

        </div>

    </div>

    <div class="alert alert-success success-import" style="display:none;">
        <p>Done importing, processed <strong class="counter">N</strong> items! Reload this page to refresh the groups!</p>
    </div>
    <div class="alert alert-success success-find" style="display:none;">
        <p>Done searching for translations, found <strong class="counter">N</strong> items!</p>
    </div>
    <div class="alert alert-success success-publish-all" style="display:none;">
        <p>Done publishing the translations for all group!</p>
    </div>

</div>
@endsection