<b>@lang('about.bot')</b><br>
@lang('about.name',['value'=>config('telegram.bot.name')])<br>
@lang('about.username',['value'=>config('telegram.bot.username')])<br>
@lang('about.version',['value'=>config('telegram.bot.version')])<br>

@if(config('telegram.bot.source'))
@lang('about.source',['value'=>'<a href="'.config('telegram.bot.source').'">Github</a>'])<br>
@endif

@if(config('telegram.bot.changelog'))
@lang('about.changelog',['value'=>'<a href="'.config('telegram.bot.changelog').'">Telegraph</a>'])<br>
@endif
<br>

<b>@lang('about.developer')</b><br>
@lang('about.name',['value'=>config('telegram.developer.name')])<br>
@lang('about.username',['value'=>config('telegram.developer.username')])<br>
@lang('about.email',['value'=>config('telegram.developer.email')])<br>
@lang('about.website',['value'=>config('telegram.developer.website')])<br>