@extends('translation-manager::layout')

@section('body')

@include('translation-manager::header_section')

<script>
    jQuery(document).ready(function($){

        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                console.log('beforesend');
                settings.data += "&_token=<?php echo csrf_token() ?>";
            }
        });

        $('.form-import').on('ajax:success', function (e, data) {
            var msg = "{{escapeJS(trans('translation-manager::translation_manager.ImportingDoneMsg'))}}";
            var msgFilled = msg.replace(":NUM", data.counter);
            alert(msgFilled);
            window.location.reload();
        });

        $('.form-find').on('ajax:success', function (e, data) {
            var msg = "{{escapeJS(trans('translation-manager::translation_manager.SearchingDoneMsg'))}}";
            var msgFilled = msg.replace(":NUM", data.counter);
            alert(msgFilled);
            window.location.reload();
        });

        $('.form-publish-all').on('ajax:success', function (e, data) {
            alert("{{escapeJS(trans('translation-manager::translation_manager.PublishingDoneMsg'))}}");
        });
    })
</script>

<div class="container-fluid">

    <div class="row" style="margin:0px 15px 15px 15px;">
        
        <form id="form_import_groups" class="form-import" method="POST" action="{{action('\Iivanov2\TranslationManager\Controller@postImport')}}" data-remote="true" role="form" style="display: inline;">
            @csrf
            <input id="import_or_update" name="import_or_update" type="hidden" value="0">

            <div class="btn-group">
                <button onclick="$('#import_or_update').submit();" type="button" class="btn btn-success" data-disable-with="Loading..." data-toggle="tooltip" data-placement="top" 
                title="{{trans('translation-manager::translation_manager.ImportGroupsBtnTooltip')}}">
                    {{trans('translation-manager::translation_manager.ImportGroupsBtn')}}
                </button>
                <button onclick="$('#import_or_update').val(1);$('#import_or_update').submit();" type="button" class="btn btn-success" data-disable-with="Loading..." data-toggle="tooltip" data-placement="top" 
                title="{{trans('translation-manager::translation_manager.UpdateGroupsBtnTooltip')}}">
                    {{trans('translation-manager::translation_manager.UpdateGroupsBtn')}}
                </button>
            </div>
        </form>

        <form class="form-find" method="POST" action="{{action('\Iivanov2\TranslationManager\Controller@postFind')}}" data-remote="true" role="form" style="display: inline;"
                data-confirm="{{trans('translation-manager::translation_manager.ConfirmScanningFilesMsg')}}">
            @csrf
            <button type="submit" class="btn btn-info" data-disable-with="Searching..." data-toggle="tooltip" data-placement="top" 
            title="{{trans('translation-manager::translation_manager.ScanFilesBtnTooltip')}}">
                {{trans('translation-manager::translation_manager.ScanFilesBtn')}}
            </button>
        </form>

        <form class="form-publish-all" method="POST" action="{{action('\Iivanov2\TranslationManager\Controller@postPublish', '*')}}" data-remote="true" role="form" style="display: inline;"
            data-confirm="{{trans('translation-manager::translation_manager.ConfirmPublishingMsg')}}">
            @csrf
            <button type="submit" class="btn btn-primary" data-disable-with="Publishing..."
                data-toggle="tooltip" data-placement="top" title="{{trans('translation-manager::translation_manager.PublishTranslationsTooltip')}}">
                {{trans('translation-manager::translation_manager.PublishTranslationsBtn')}}
            </button>
        </form>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <form class="form-inline" method="GET" action="{{action('\Iivanov2\TranslationManager\Controller@postListGroup')}}">
                    <label>{{trans('translation-manager::translation_manager.SelectGroupToTranslateLabel')}}</label>
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
                <form class="form-inline" method="POST" action="{{ action('\Iivanov2\TranslationManager\Controller@postSearchString') }}">
                    @csrf
                    <label>{{trans('translation-manager::translation_manager.SearchingTextLabel')}}</label>
                    <input type="text" class="form-control" name="string_to_find">
                    <input type="submit" class="btn btn-primary" value="{{trans('translation-manager::translation_manager.SearchBtn')}}">
                </form>
                <hr>
            </div>

        </div>

    </div>

</div>
@endsection