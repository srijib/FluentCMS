<?php

// This controller handles the upload and the deletion of an Avatar
// image for the user profile.

Yii::import('application.modules.user.controllers.YumController');

class YumAvatarController extends YumController
{
    public function actionRemoveAvatar($id = null)
    {
        if(is_null($id)){
            $id = Yii::app()->user->id;
        }

        if (Yii::app()->user->isAdmin()) {
            // Setting admin layout
            $this->layout = 'application.modules.admin.views.layouts.admin';
        } else {
            $this->layout = 'application.modules.author.views.layouts.cabinet';
        }
        $model = YumUser::model()->findByPk($id);
        $model->avatar = '';
        $model->save();
        $this->redirect('editAvatar');
    }

    public function actionEnableGravatar($id = null)
    {
        if(is_null($id)){
            $id = Yii::app()->user->id;
        }

        if (Yii::app()->user->isAdmin()) {
            // Setting admin layout
            $this->layout = 'application.modules.admin.views.layouts.admin';
        } else {
            $this->layout = 'application.modules.author.views.layouts.cabinet';
        }
        $model = YumUser::model()->findByPk($id);
        $model->avatar = 'gravatar';
        $model->save();
        $this->redirect(array(
            '//profile/profile/view', 'id' => $model->id));
    }

    public function beforeAction($action)
    {
        // Disallow guests
        if (Yii::app()->user->isGuest)
            $this->redirect(Yum::module()->loginUrl);

        return parent::beforeAction($action);
    }

    public function actionEditAvatar($id = null )
    {
        if(is_null($id)){
            $id = Yii::app()->user->id;
        }

        if (Yii::app()->user->isAdmin()) {
            // Setting admin layout
            $this->layout = 'application.modules.admin.views.layouts.admin';
        } else {
            $this->layout = 'application.modules.author.views.layouts.cabinet';
        }
        $model = YumUser::model()->findByPk($id);

        if (isset($_POST['YumUser'])) {
            $model->attributes = $_POST['YumUser'];
            $model->setScenario('avatarUpload');

            if (Yum::module('avatar')->avatarMaxWidth != 0)
                $model->setScenario('avatarSizeCheck');

            $model->avatar = CUploadedFile::getInstanceByName('YumUser[avatar]');
            if ($model->validate()) {
                if ($model->avatar instanceof CUploadedFile) {

                    // Prepend the id of the user to avoid filename conflicts
                    $filename = Yum::module('avatar')->avatarPath . '/' . $model->id . '_' . $_FILES['YumUser']['name']['avatar'];
                    $model->avatar->saveAs($filename);
                    $model->avatar = $filename;
                    if ($model->save()) {
                        Yum::setFlash(Yum::t('The image was uploaded successfully'));
                        Yum::log(Yum::t('User {username} uploaded avatar image {filename}', array(
                            '{username}' => $model->username,
                            '{filename}' => $model->avatar)));
                        $this->redirect('editAvatar');
                    }
                }
            }
        }

        $this->render('edit_avatar', array('model' => $model));
    }
}
