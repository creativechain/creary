<div class="step-3">

    <div class="boxed boxed--border">
        <div class="section-title-step">
            <h4 class="title-steps">{{ __('lang.PUBLISH.DOWNLOAD_TITLE') }}</h4>
            <span class="description-step-title">{{ __('lang.PUBLISH.DOWNLOAD_SUBTITLE') }}</span>
        </div>
        <form action="" class="row">
            <div v-if="editablePost && editablePost.download.size > 0 && !editablePost.downloadUploaded" class="col-md-12">
                <span class="error-color-form">@{{ stringFormat(lang.PUBLISH.RELOAD_DOWNLOAD_FILE, editablePost.download.name) }}</span>
            </div>
            <div class="col-md-12">
                <input type="file" v-on:input="onInputDownloadFile" class="form-control-file" id="exampleFormControlFile1" />
            </div>
            <div class="col-md-8">
                <label>{{ __('lang.PUBLISH.PRICE') }}</label>
                <input class="validate-required" v-model="downloadFile.price" type="number" min="0" step="0.001" placeholder="{{ __('lang.PUBLISH.INPUT_PRICE') }}" />
            </div>
            <div class="col-md-4">
                <label></label>

                <div class="input-select">
                    <select v-model="downloadFile.currency">
                        <option selected="" value="CREA">CREA</option>
                        <option value="CBD">CREA DOLLAR (CBD)</option>
                    </select>
                </div>
            </div>
        </form>
    </div>

</div>
