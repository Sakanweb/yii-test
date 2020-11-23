<?php
use yii\helpers\Html;
use yii\helpers\Url;
/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>
<script type="text/javascript">
    var maximum_number_imports = <?= $import_file_info['maximum_number_imports']; ?>;
    var file_types = <?= json_encode($import_file_info['file_types']); ?>;
    var redirect_url = "<?= Yii::$app->getUrlManager()->createUrl($import_file_info['redirect_url']); ?>";

</script>

<div class="site-index">
    <form enctype="multipart/form-data" id="fileform" method="post" action="<?= Yii::$app->getUrlManager()->createUrl('site/index'); ?>">
        <input type="hidden" name="<?= Yii::$app->request->csrfParam; ?>" value="<?= Yii::$app->request->csrfToken; ?>" />
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <h4>Store Name:</h4>
                    <input type="text" class="form-control" name="storeName" value="">
                </div>
            </div>
            <div class="col-sm-4">
                <table class="table table-bordered" id="importList"></table>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6" >
                <h4>Select File's:</h4>
                <button id="add_field_button">Add More Fields</button>
                <div class="form-inline" id="input_fields_wrap">
                    <div class="form-group mb-2">
                        <input type="file" class="form-control" name="importFile[]" accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    </div>
                </div>
                <button type="button" id="upload" class="btn btn-primary">Start Upload</button>
            </div>
        </div>
    </form>
</div>
