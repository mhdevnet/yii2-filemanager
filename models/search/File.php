<?php

namespace nitm\filemanager\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * FilesSearch represents the model behind the search form about `nitm\filemanager\models\Files`.
 */
class File extends BaseSearch
{
	use \nitm\filemanager\traits\FileTraits;
}
