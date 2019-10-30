<?php
/**
 * Created by PhpStorm.
 * User: Bogdan S
 * Date: 09.08.2019
 * Time: 15:05
 */

use yii\helpers\Html;
?>
<p>Вы ввели следующую информацию:</p>

<ul>
    <li><label>Name</label>: <?= Html::encode($model->name) ?></li>
    <li><label>Email</label>: <?= Html::encode($model->email) ?></li>
</ul>