<div class="container">
	<h3 class="page-title">{{ __e('Error 404 : Page introuvable') }}</h3>

	<p class="promise">
		{{ __e($message) }}
	</p>
	<p class="back-link">
        <a class="back" href="{{ url() }}">{{ __e('Go Back') }}</a>
    </p>
    <!-- <div class="footer">
        &copy; {!! date('Y') !!} {{ config('name') }}
    </div> -->
</div>