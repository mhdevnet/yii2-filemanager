<?php

namespace nitm\filemanager\traits;

use Yii;
use yii\base\Model;
use yii\base\Event;
use nitm\models\User;
use nitm\widgets\models\Category;
use nitm\filemanager\models\Image;
use nitm\helpers\Relations as RelationsHelper;
use nitm\traits\Relations as NitmRelations;
use nitm\helpers\ArrayHelper;

/**
 * Class Replies
 * @package nitm\module\models
 */

trait Relations
{
	/**
	 * File based relations
	 */
	protected function getFileRelationQuery($className, $link=null, $options=[], $many=false)
	{
		$link = !is_array($link) ? ['remote_id' => 'id'] : $link;
		$options = is_array($options) ? $options : (array)$options;
		$options['select'] = isset($options['select']) ? $options['select'] : ['id', 'remote_id', 'remote_type'];
		$options['with'] = array_merge(ArrayHelper::getValue($options, 'with', []), ['author', 'last', 'count', 'newCount']);
		$options['andWhere'] = isset($options['andWhere']) ? $options['andWhere'] : ['remote_type' => $this->isWhat()];
		$options['groupBy'] = isset($options['groupBy']) ? $options['groupBy'] : array_merge($link, [
			'remote_type' => $this->isWhat(),
			'hash' => null
		]);
		return $this->getRelationQuery($className, $link, $options, $many);
	}

	/**
	 * File based relations
	 */
	protected function getFileRelationModelQuery($className, $link=null, $options=[])
	{
		$link = !is_array($link) ? ['remote_id' => 'id'] : $link;
		$options['select'] = isset($options['select']) ? $options['select'] : ['remote_id', 'remote_type'];
		$options['with'] = array_merge(ArrayHelper::getValue($options, 'with', []), ['count', 'newCount']);
		$options['andWhere'] = isset($options['andWhere']) ? $options['andWhere'] : ['remote_type' => $this->isWhat()];
		$options['groupBy'] = isset($options['groupBy']) ? $options['groupBy'] : array_merge($link, [
			'remote_type' => $this->isWhat()
		]);
		return $this->getRelationQuery($className, $link, $options);
	}

	protected function getCachedFileRelationModel($className, $idKey=null, $many=false, $options=[])
	{
		$relation = \nitm\helpers\Helper::getCallerName();
		$options['construct'] = isset($options['construct']) ? $options['construct'] : [
			'remote_id' => $this->getId(),
			'remote_type' => $this->isWhat()
		];
		$idKey = is_null($idKey) ? ['getId', 'isWhat'] : $idKey;
		return $this->getCachedRelation($idKey, $className, $options, $many, $relation);
	}

	protected function getFileRelationModel($className, $relation=null, $idKey=null, $many=false, $options=[])
	{
		$relation = $relation ?: \nitm\helpers\Helper::getCallerName();
		$options['construct'] = isset($options['construct']) ? $options['construct'] : [
			'remote_id' => $this->getId(),
			'remote_type' => $this->isWhat()
		];
		$idKey = is_null($idKey) ? ['getId', 'isWhat'] : $idKey;
		return $this->resolveRelation($idKey, $className, true, $options, $many, $relation);
	}

	/**
	 * Get all the images for this entity
	 * @param boolean $thumbnails Get thumbnails as well?
	 * @param boolean $default Get the default image as well?
	 */
	public function getImages($thumbnails=false, $default=false)
	{
        return Image::getImagesFor($this, $thumbnails, $default);
	}

	public function imageList($idsOnly=false)
	{
		return ArrayHelper::filter($this->images, $idsOnly, function ($image) {
			$thumb = $image->getIcon('medium');
			if(!$thumb->height || !$thumb->width)
				$image->updateMetadataSizes('medium');
			if(!$image->height || !$image->width)
				$image->updateSizes();
			return [
				'id' => $image->getId(),
				'title' => ucfirst($image->remote_type).' Image',
				'thumb' => $thumb->url('medium'),
				'src' => $thumb->url('medium'),
				'url' => $image->url(),
				'height' => $thumb->height,
				'width' => $thumb->width,
			];
		});
	}

	public function images($useCache=false)
	{
		return $this->resolveRelation('id', \nitm\filemanager\models\Image::className(), $useCache, [], true, 'images');
	}

	public function image()
	{
		return $this->getFileRelationModel(\nitm\filemanager\models\Image::className(), 'image');
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->getFileRelationModelQuery(\nitm\filemanager\models\Image::className());
    }

	/**
	 * Get the main icon for this entity
	 */
	public function getIcon()
	{
        return Image::getIconFor($this);
	}

	/**
	 * Get metadata, either from key or all metadata
	 * @param string $key
	 * @return mixed
	 */
	public function icon()
	{
		return $this->getFileRelationModel(\nitm\filemanager\models\Image::className(), 'icon');
	}

    /**
	 * Get files relation
	 * @param array $options Options for the relation
     * @return \yii\db\ActiveQuery
     */
    public function getFiles($options=[])
    {
		$options = array_merge([
			'orderBy' => ['id' => SORT_DESC],
			'select' => '*'
		], $options);
        return $this->getFileRelationQuery(\nitm\filemanager\models\File::className(), null, $options, true);
    }

	public function files($useCache=false)
	{
		return $this->resolveRelation('id', \nitm\filemanager\models\File::className(), $useCache, [], true, 'files');
	}

	public function fileList($idsOnly=false)
	{
		return ArrayHelper::filter($this->files(), $idsOnly, function ($file) {
			return [
				'title' => ucfirst($file->remote_type).' Image',
				'src' => $file->url(),
				'url' => $file->url(),
			];
		});
	}

	public function file()
	{
		return $this->getFileRelationModel(\nitm\filemanager\models\File::className(), 'file');
	}

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->getFileRelationModelQuery(\nitm\filemanager\models\File::className());
    }

	public function getImageMeta()
	{
        return $this->hasOne(Image::className(), ['remote_id' => 'id'])
            ->select(['remote_type', 'remote_id'])
    		->where(['remote_type' => $this->isWhat()])
            ->groupBy(['remote_id', 'remote_id'])
            ->with([
                'count'
            ]);
	}

	public function imageMeta()
	{
    	return $this->resolveRelation('id', Image::className(), false, [
            'remote_type' => $this->isWhat(),
            'remote_id' => $this->getId()
        ], false, 'imageMeta');
	}

	public function getFileMeta()
	{
        return $this->hasOne(Image::className(), ['remote_id' => 'id'])
            ->select(['remote_type', 'remote_id'])
    		->where(['remote_type' => $this->isWhat()])
            ->groupBy(['remote_id', 'remote_id'])
            ->with([
                'count'
            ]);
	}

	public function fileMeta()
	{
    	return $this->resolveRelation('id', Image::className(), false, [
            'remote_type' => $this->isWhat(),
            'remote_id' => $this->getId()
        ], false, 'fileMeta');
	}
 }
?>
