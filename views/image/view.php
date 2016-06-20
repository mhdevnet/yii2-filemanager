<?php
use yii\helpers\Html;
use yii\bootstrap\ButtonGroup;
use kartik\widgets\ActiveForm;
use kartik\widgets\ActiveField;
use kartik\grid\GridView;
use nitm\helpers\Icon;

/**
 * @var yii\web\View $this
 * @var provisioning\models\ProvisioningImage $model
 */

$options = isset($options) ? $options : [
	'id' => 'images',
	'role' => 'imagesContainer'
];

if(!isset($this->title))
	$this->title = $model->file_name;

if(!isset($noBreadcrumbs) || (isset($noBreadcrumbs) && !$noBreadcrumbs))
	echo \yii\widgets\Breadcrumbs::widget(['links' => $this->params['breadcrumbs']]);

if(!is_callable('getUrl')) {
	function getUrl($action, $model, $options=[]) {
		return \Yii::$app->urlManager->createUrl(array_merge(['/'.$model->isWhat().'/'.$action.'/'.$model->getId()], $options));
	}
}
?>

<?php
	echo Html::beginTag('div', [
		"class" => 'well well-sm row media',
		'role' => 'statusIndicator'.$model->getId().' imageContainer '.($model->isDefault() ? 'defaultImage' : 'extraImage'),
		'id' => 'image'.$model->getId()
	]);
?>

	<div class="media">
		<div class="media-left media-middle">
			<?php
				if(\Yii::$app->user->identity->lastActive() < strtotime($model->created_at)
					|| \Yii::$app->user->identity->lastActive() < strtotime($model->updated_at))
					echo  \nitm\widgets\activityIndicator\ActivityIndicator::widget([
							'type' => 'create',
							'size' => 'large',
					]);
			?>
			<?= Html::a($model->icon->getIconHtml('small', [
				'class' => 'thumbnail thumbnail small media-object '.($model->isDefault() ? 'default' : ''),
				'url' => $model->url('small')
			]), $model->url('small')) ?>
		</div>
		<div class="media-body">
			<?= Html::tag('h4', $model->file_name) ?>
			<?php /* Html::tag('strong', $model->getId(), ['style' => 'font-size: 24px']); */ ?>
			<div class="media-middle">
				<?= $model->getSize(); ?>
			</div>
		</div>
		<div class="media-right media-middle" style="min-width: 30%">
			<?= ButtonGroup::widget([
				'encodeLabels' => false,
				'buttons' => [
					'delete' => [
						'tagName' => 'a',
						'label' => Icon::forAction('trash').Html::tag('span', ' Delete', [
							'class' => 'visible-lg'
						]),
						'options' => [
							'class' => 'btn btn-danger',
							'title' => \Yii::t('yii', 'Delete Image'),
							'data-pjax' => '0',
							'role' => "deleteAction deleteImage metaAction",
							'data-parent' => '#image'.$model->getId(),
							'data-method' => 'post',
							'data-action' => 'delete',
							'data-url' => getUrl('delete', $model, ['__format' => 'json'])
						]
					],
					'info' => [
						'label' => Icon::forAction('info-sign').Html::tag('span', ' Info', [
							'class' => 'visible-lg'
						]),
						'options' => [
							'class' => 'btn btn-info',
							'title' => \Yii::t('yii', 'Show more Information'),
							'data-pjax' => '0',
							'role' => "visibility",
							'data-id' => 'image-info'.$model->getId(),
						]
					],
					'default' => [
						'tagName' => 'a',
						'label' => Icon::forAction('check').Html::tag('span', ' Default', [
							'class' => 'visible-lg'
						]),
						'options' => [
							'class' => 'btn btn-success '.($model->isDefault() ? 'hidden' : ''),
							'title' => \Yii::t('yii', 'Set this image as default'),
							'data-pjax' => '0',
							'role' => "toggleDefaultImage",
							'data-id' => 'image-default'.$model->getId(),
							'data-parent' => 'image'.$model->getId(),
							'href' => getUrl('default', $model)
						]
					],
					'get' => [
						'tagName' => 'a',
						'label' => Icon::forAction('download').Html::tag('span', ' Download', [
							'class' => 'visible-lg'
						]),
						'options' => [
							'class' => 'btn btn-default',
							'title' => \Yii::t('yii', 'Download Image'),
							'data-pjax' => '0',
							'inline' => true,
							'data-parent' => 'image'.$model->getId(),
							'data-method' => 'get',
							'_target' => 'new',
							'href' => $model->url()
						]
					],
				],
			]); ?>
		</div>
	</div>
	<div class="col-sm-12 hidden" id='image-info<?=$model->getId();?>'>
		<h2>Metadata Information</h2>
		<div class="well">
		<?php
			$metaInfo = \nitm\widgets\metadata\StatusInfo::widget([
			'items' => [
				[
					'blamable' => $model->author(),
					'date' => $model->created_at,
					'value' => $model->created_at,
					'label' => [
						'true' => "Created On ",
					]
				],
				[
					'value' => $model->type,
					'label' => [
						'true' => "Image type ",
					]
				],
				[
					'value' => $model->getSize(),
					'label' => [
						'true' => "Image size ",
					]
				],
			]
		]);


		$shortLink = \nitm\widgets\metadata\ShortLink::widget([
			'label' => 'Url',
			'url' => $model->url(),
			'header' => $model->file_name,
			'type' => (\Yii::$app->request->isAjax ? 'page' : 'modal'),
			'size' => 'large'
		]).
		\nitm\widgets\metadata\ShortLink::widget([
			'label' => 'Path',
			'url' => $model->getRealPath(),
			'header' => $model->file_name,
			'type' => (\Yii::$app->request->isAjax ? 'page' : 'modal'),
			'size' => 'large'
		]);
		echo Html::tag('tr',
			Html::tag('td', $metaInfo.$shortLink, [
				'colspan' => 10,
			]), [
			'class' => 'hidden',
			'id' => 'image-info'.$model->getId()
		]); ?>
		</div>
	</div>
<?= Html::endTag('div') ?>
