@extends('index')
@section('content')
<div class="banner page bg relative" data-background="{<BG_TRUYEN.1.-1>}" data-background-webp="{<BG_TRUYEN.1.-1>}">
	<div class="text">
		<div class="container">
			<h1 class="title_pro">{(name)}</h1>
			{%BREADCRUMB%}
		</div>
	</div>
</div>
<div class="container">
	<h1 class="text-uppercase my-5 text-center fs-20">{(dataitem.name)}</h1>
	<p class="fs-16 w-75 mx-auto my-4">
		{(dataitem.short_content)}
	</p>
	<div class="s-content">
		{(dataitem.content)}
	</div>
</div>
@stop