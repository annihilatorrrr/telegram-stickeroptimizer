@lang('settings.title')<br>
@lang('settings.description')<br>
<br>
@lang('settings.news'): {{ $news ? __('settings.enabled') : __('settings.disabled') }}<br>
@lang('settings.language.title'): {{ $language }}