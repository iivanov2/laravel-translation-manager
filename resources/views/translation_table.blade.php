@extends('translation-manager::layout')

@include('translation-manager::header_section')

@section('body')

<script>
    jQuery(document).ready(function($){

        $.ajaxSetup({
            beforeSend: function(xhr, settings) {
                settings.data += "&_token={{csrf_token()}}";
            }
        });

        $('.editable').editable({
                params: function(params) {
                    // add additional params from data-attributes of trigger element
                    params.locale = $(this).editable().data('locale');
                    params.group = $(this).editable().data('group');
                    params.key = $(this).editable().data('key');
                    return params;
                }
            }).on('hidden', function(e, reason){
            var locale = $(this).data('locale');
            if(reason === 'save'){
                $(this).removeClass('status-0').addClass('status-1');
            }
            if(reason === 'save' || reason === 'nochange') {
                var $next = $(this).closest('tr').next().find('.editable.locale-'+locale);
                setTimeout(function() {
                    $next.editable('show');
                }, 300);
            }
        });

        $('.form-publish-group').on('ajax:success', function (e, data) {
            alert("{{trans('translation-manager::translation_manager.PublishingDoneMsg')}}");
        });
    })
</script>

<div class="container-fluid">
    <a href="/admin/translation-manager" class="btn btn-primary" role="button">&#8656; {{trans('translation-manager::translation_manager.GoToDashboardBtn')}}</a>

    @if(!empty(request()->group))
    <form class="form-publish-group" method="POST" action="{{action('\Iivanov2\TranslationManager\Controller@postPublish', request()->group)}}" data-remote="true" role="form" style="display: inline;"
        data-confirm="{{trans('translation-manager::translation_manager.ConfirmPublishingGroupMsg')}}">
        @csrf
        <button type="submit" class="btn btn-primary" 
        data-disable-with="{{trans('translation-manager::translation_manager.Publishing_')}}"
            data-toggle="tooltip" data-placement="top" 
            title="{{trans('translation-manager::translation_manager.PublishTranslationsTooltip')}}">
                {{trans('translation-manager::translation_manager.PublishGroupBtn')}}
        </button>
    </form>
    @endif

    <h4 style="margin-top:15px;">
        @if(!empty(request()->group))
            <b>{{trans('translation-manager::translation_manager.Group')}}: <u>{{request()->group}}</u></b>,
        @endif
        {{trans('translation-manager::translation_manager.TotalNum')}}: {{$numTranslations}}, 
        {{trans('translation-manager::translation_manager.UnpublishedNum')}}: {{$numChanged}}
    </h4>
    <table class="table table-bordered table-responsive">
        <thead>
        <tr>
            <th width="15%">Key</th>
            @foreach($locales as $locale)
                <th>{{$locale}}</th>
            @endforeach
        </tr>
        </thead>
        <tbody>
    
        @foreach($translationsBundle as $key => $translations)
            <?php
                $translationGroup="";
                foreach($translations as $translationLocale => $translationValue) {
                    if(!empty($translationValue->group)) {
                        $translationGroup=$translationValue->group;
                    }
                }
            ?>
            <tr>
                <td>
                    {!!htmlentities($key, ENT_QUOTES, 'UTF-8', false)!!}
                </td>
                @foreach($locales as $locale)
                    <td>
                        <!-- x-editable http://vitalets.github.io/x-editable/docs.html -->
                        <a href="#edit" class="editable status-@if(!empty($translations[$locale])){{$translations[$locale]->status}}@else{{0}}@endif"
                            data-type="textarea"
                            data-tpl="<textarea class=&quot;form-control input-large&quot; rows=&quot;7&quot; cols=&quot;100&quot;></textarea>"
                            data-pk="@if(!empty($translations[$locale])){{$translations[$locale]->id}}@else{{0}}@endif"
                            data-locale="{{$locale}}"
                            data-key="{!!htmlentities($key, ENT_QUOTES, 'UTF-8', false)!!}"
                            data-group="{{$translationGroup}}"
                            data-url="{{action('\Iivanov2\TranslationManager\Controller@postTranslate')}}"
                            data-title="{{trans('translation-manager::translation_manager.EnterTranslationTitle')}}">@if(!empty($translations[$locale])){{$translations[$locale]->value}}@endif</a>
                        </a>
                    </td>
                @endforeach
            </tr>
        @endforeach
        </tbody>
    </table>

</div>
@endsection
