<?php
/** @var \dosamigos\fileupload\FileUploadUI $this */
use yii\helpers\Html;

$context = $this->context;
$options = [
    'role' => 'uploadByUrl',
    'id' => $context->options['id'],
];
?>
    <!-- The file upload form used as target for the file upload widget -->
<?= Html::beginTag('div', $options); ?>
    <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
    <div class="row fileupload-url-buttonbar">
        <div class="col-lg-7 col-sm-12">
            <!-- The fileinput-button span is used to style the file input field as button -->
            <span class="btn btn-success fileinput-button" role="addUrl" data-file-upload-ui='#<?= $context->options['id'] ?>'>
                <i class="glyphicon glyphicon-plus"></i>
                <span class="hide-sm"><?= Yii::t('fileupload', 'Add urls') ?>...</span>

                <?= $context->model instanceof \yii\base\Model && $context->attribute !== null
                    ? Html::activeFileInput($context->model, $context->attribute, $context->fieldOptions)
                    : Html::fileInput($context->name, $context->value, $context->fieldOptions);?>
            </span>
            <a class="btn btn-primary start">
                <i class="glyphicon glyphicon-upload"></i>
                <span class="hide-sm"><?= Yii::t('fileupload', 'Start upload') ?></span>
            </a>
            <a class="btn btn-warning cancel">
                <i class="glyphicon glyphicon-ban-circle"></i>
                <span class="hide-sm"><?= Yii::t('fileupload', 'Cancel upload') ?></span>
            </a>
            <a class="btn btn-danger delete">
                <i class="glyphicon glyphicon-trash"></i>
                <span><?= Yii::t('fileupload', 'Delete') ?></span>
            </a>
            <input type="checkbox" class="toggle">
            <!-- The global file processing state -->
            <span class="fileupload-process"></span>
        </div>
        <!-- The global progress state -->
        <div class="col-lg-5 col-sm-12 fileupload-progress fade">
            <!-- The global progress bar -->
            <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                <div class="progress-bar progress-bar-success" style="width:0%;"></div>
            </div>
            <!-- The extended global progress state -->
            <div class="progress-extended">&nbsp;</div>
        </div>
    </div>
    <!-- The table listing the files available for upload/download -->
    <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
<?= Html::endTag('div');?>
