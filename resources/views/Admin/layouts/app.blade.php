<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" lang="en-US">

<head>
    <title>SurfsideMedia</title>
    <meta charset="utf-8">
    <meta name="author" content="themesflat.com">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    @include('admin.layouts.styles')
</head>

<body class="body">
    <div id="wrapper">
        <div id="page" class="">
            <div class="layout-wrap">

                 {{-- <div id="preload" class="preload-container">
    <div class="preloading">
        <span></span>
    </div>
</div> --}}

                @include('admin.layouts.sidebar')
                <div class="section-content-right">

                    @include('admin.layouts.header')
                    @yield('content')

                </div>
            </div>
        </div>
    </div>

    @include('admin.layouts.scripts')
</body>

</html>
