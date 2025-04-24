@extends('admin.layouts.app')
@section('content')
<div class="main-content">
    <div class="main-content-inner">
        <div class="main-content-wrap">
            <div class="flex items-center flex-wrap justify-between gap-20 mb-27">
                <h3>Brand Information</h3>
                <ul class="breadcrumbs flex items-center flex-wrap justify-start gap-10">
                    <li>
                        <a href="{{ route('admin.index') }}">
                            <div class="text-tiny">Dashboard</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <a href="{{ route('admin.brands') }}">
                            <div class="text-tiny">Brands</div>
                        </a>
                    </li>
                    <li>
                        <i class="icon-chevron-right"></i>
                    </li>
                    <li>
                        <div class="text-tiny">
                            {{ isset($brand) ? 'Edit Brand' : 'New Brand' }}
                        </div>
                    </li>
                </ul>
            </div>

            <!-- New Category -->
            <div class="wg-box">
                <form class="form-new-product form-style-1"
                      action="{{ isset($brand) ? route('admin.brands.update', $brand) : route('admin.brands.store') }}"
                      method="POST" enctype="multipart/form-data">
                    @csrf
                    @if (isset($brand))
                        @method('PUT')
                    @endif

                    <fieldset class="name">
                        <div class="body-title">Brand Name <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Brand Name" name="name" tabindex="0"
                               value="{{ old('name', isset($brand) ? $brand->name : '') }}" required>
                    </fieldset>
                    @error('name')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror

                    <fieldset class="name">
                        <div class="body-title">Brand Slug <span class="tf-color-1">*</span></div>
                        <input class="flex-grow" type="text" placeholder="Brand Slug" name="slug" tabindex="0"
                               value="{{ old('slug', isset($brand) ? $brand->slug : '') }}" required>
                    </fieldset>
                    @error('slug')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror

                    <fieldset>
                        <div class="body-title">Upload Image <span class="tf-color-1">*</span></div>
                        <div class="upload-image flex-grow">
                            <div class="item" id="imgpreview" style="{{ isset($brand) ? 'display:block' : 'display:none' }}">
                                @if (isset($brand))
                                    <img src="{{ asset('uploads/brands/' . $brand->image) }}" class="effect8" alt="">
                                @else
                                    <img src="upload-1.html" class="effect8" alt="">
                                @endif
                            </div>

                            <div id="upload-file" class="item up-load">
                                <label class="uploadfile" for="myFile">
                                    <span class="icon">
                                        <i class="icon-upload-cloud"></i>
                                    </span>
                                    <span class="body-text">Drop your image here or select <span class="tf-color">click to browse</span></span>
                                    <input type="file" id="myFile" name="image" accept="image/*">
                                </label>
                            </div>
                        </div>
                    </fieldset>
                    @error('image')
                        <span class="alert alert-danger text-center">{{ $message }}</span>
                    @enderror

                    <div class="bot">
                        <button class="tf-button w208" type="submit">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $(function() {
        // Handle image preview
        $("#myFile").on("change", function() {
            const photoInp=$("#myFile");
            const [file] = this.files;
            if (file) {
                $("#imgpreview img").attr('src', URL.createObjectURL(file));
                $("#imgpreview").show();
            }
        });

        // Automatically generate slug based on brand name
        $("input[name='name']").on("input", function() {
            $("input[name='slug']").val(StringToSlug($(this).val()));
        });
    });

    // Function to generate slug
    function StringToSlug(Text) {
        return Text.toLowerCase()
            .replace(/[^\w ]+/g, "")
            .replace(/ +/g, "-");
    }
</script>
@endpush
