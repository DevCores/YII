<?php
/**
 * @var $this yii\web\View
 */
use backend\assets\BackendAsset;
use backend\models\SystemLog;
use backend\widgets\Menu;
use common\models\TimelineEvent;
use common\widgets\Alert;
use common\models\User;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\log\Logger;
use yii\widgets\Breadcrumbs;

$bundle = BackendAsset::register($this);
?>
<?php $this->beginContent('@backend/views/layouts/base.php'); ?>
<div class="wrapper">
    <!-- header logo: style can be found in header.less -->
    <header class="main-header">
        <a href="<?php echo Yii::$app->urlManagerFrontend->createAbsoluteUrl('/') ?>" class="logo">
            <!-- Add the class icon to your logo image or logo icon to add the margining -->
            <?php echo Yii::$app->name ?>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top" role="navigation">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="offcanvas" role="button">
                <span class="sr-only"><?php echo Yii::t('backend', 'Toggle navigation') ?></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>
            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li id="timeline-notifications" class="notifications-menu">
                        <a href="<?php echo Url::to(['/timeline-event/index']) ?>">
                            <i class="fa fa-bell"></i>
                            <span class="label label-success">
                                    <?php echo TimelineEvent::find()->today()->count() ?>
                                </span>
                        </a>
                    </li>
                    <!-- Notifications: style can be found in dropdown.less -->
                    <li id="log-dropdown" class="dropdown notifications-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <i class="fa fa-warning"></i>
                            <span class="label label-danger">
                                <?php echo SystemLog::find()->count() ?>
                            </span>
                        </a>
                        <ul class="dropdown-menu">
                            <li class="header"><?php echo Yii::t('backend', 'You have {num} log items', ['num' => SystemLog::find()->count()]) ?></li>
                            <li>
                                <!-- inner menu: contains the actual data -->
                                <ul class="menu">
                                    <?php foreach (SystemLog::find()->orderBy(['log_time' => SORT_DESC])->limit(5)->all() as $logEntry): ?>
                                        <li>
                                            <a href="<?php echo Yii::$app->urlManager->createUrl(['/log/view', 'id' => $logEntry->id]) ?>">
                                                <i class="fa fa-warning <?php echo $logEntry->level === Logger::LEVEL_ERROR ? 'text-red' : 'text-yellow' ?>"></i>
                                                <?php echo $logEntry->category ?>
                                            </a>
                                        </li>
                                    <?php endforeach; ?>
                                </ul>
                            </li>
                            <li class="footer">
                                <?php echo Html::a(Yii::t('backend', 'View all'), ['/log/index']) ?>
                            </li>
                        </ul>
                    </li>
                    <!-- User Account: style can be found in dropdown.less -->
                    <li class="dropdown user user-menu">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">
                            <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/default-profile.jpg')) ?>"
                                 class="user-image">
                            <span><?php echo Yii::$app->user->identity->username ?> <i class="caret"></i></span>
                        </a>
                        <ul class="dropdown-menu">
                            <!-- User image -->
                            <li class="user-header light-blue">
                                <img src="<?php echo Yii::$app->user->identity->userProfile->getAvatar($this->assetManager->getAssetUrl($bundle, 'img/default-profile.jpg')) ?>"
                                     class="img-circle" alt="User Image"/>
                                <p>
                                    <?php echo Yii::$app->user->identity->username ?>
                                    <small>
                                        <?php echo Yii::t('backend', 'Member since {0, date, short}', Yii::$app->user->identity->created_at) ?>
                                    </small>
                            </li>
                            <!-- Menu Footer-->
                            <li class="user-footer">
                                <div class="pull-left">
                                    <?php echo Html::a(Yii::t('backend', 'Profile'), ['/sign-in/profile'], ['class' => 'btn btn-default btn-flat']) ?>
                                </div>
                                <div class="pull-left">
                                    <?php echo Html::a(Yii::t('backend', 'Account'), ['/sign-in/account'], ['class' => 'btn btn-default btn-flat']) ?>
                                </div>
                                <div class="pull-right">
                                    <?php echo Html::a(Yii::t('backend', 'Logout'), ['/sign-in/logout'], ['class' => 'btn btn-default btn-flat', 'data-method' => 'post']) ?>
                                </div>
                            </li>
                        </ul>
                    </li>
                    <li>
                        <?php echo Html::a('<i class="fa fa-cogs"></i>', ['/site/settings']) ?>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Left side column. contains the logo and sidebar -->
    <aside class="main-sidebar">
        <!-- sidebar: style can be found in sidebar.less -->
        <section class="sidebar">
            <!-- sidebar menu: : style can be found in sidebar.less -->
            <?php echo Menu::widget([
                'options' => ['class' => 'sidebar-menu'],
                'linkTemplate' => '<a href="{url}">{icon}<span>{label}</span>{right-icon}{badge}</a>',
                'submenuTemplate' => "\n<ul class=\"treeview-menu\">\n{items}\n</ul>\n",
                'activateParents' => true,
                'items' => [
                    [
                        'label' => Yii::t('backend', 'Main'),
                        'options' => ['class' => 'header'],
                        'visible' => (
                                Yii::$app->user->can(User::PERM_ACCESS_TO_TIMELINE) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_CONTENT)
                            ),
                    ],
                    [
                        'label' => Yii::t('backend', 'Timeline'),
                        'icon' => '<i class="fa fa-bar-chart-o"></i>',
                        'url' => ['/timeline-event/index'],
                        'badge' => TimelineEvent::find()->today()->count(),
                        'badgeBgClass' => 'label-success',
                        'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_TIMELINE),
                    ],
                    [
                        'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_CONTENT),
                        'label' => Yii::t('backend', 'Content'),
                        'url' => '#',
                        'icon' => '<i class="fa fa-edit"></i>',
                        'options' => ['class' => 'treeview'],
                        'active' => in_array(\Yii::$app->controller->id,['page','article','article-category','widget-text','widget-menu','widget-carousel']),
                        'items' => [
                            [
                                'label' => Yii::t('backend', 'Static pages'),
                                'url' => ['/page/index'],
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'page')
                            ],
                            [
                                'label' => Yii::t('backend', 'Articles'),
                                'url' => ['/article/index'],
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'article')
                            ],
                            [
                                'label' => Yii::t('backend', 'Article Categories'),
                                'url' => ['/article-category/index'],
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'article-category')
                            ],
                            [
                                'label' => Yii::t('backend', 'Carousel Widgets'),
                                'url' => ['/widget-carousel/index'],
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'widget-carousel')
                            ],
                        ]
                    ],
                    [
                        'label' => Yii::t('backend', 'Modules'),
                        'visible' => (Yii::$app->user->can(User::PERM_ACCESS_TO_FORUM) || Yii::$app->user->can(User::PERM_ACCESS_TO_RBAC)),
                        'options' => ['class' => 'header']
                    ],
                    [
                        'label' => Yii::t('backend', 'RBAC'),
                        'icon' => '<i class="fa fa-bolt"></i>',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'active' => (\Yii::$app->controller->module->id == 'rbac'),
                        'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_RBAC),
                        'items' => [
                            [
                                'label' => Yii::t('rbac-admin','Roles'),
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'role'),
                                'url' => ['/rbac/role/index'],
                            ],
                            [   
                                'label' => Yii::t('rbac-admin','Rules'),
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'rule'),
                                'url' => ['/rbac/rule/index'],
                            ],
                            [   
                                'label' => Yii::t('rbac-admin','Permissions'),
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'permission'),
                                'url' => ['/rbac/permission/index'],
                            ],
                            [
                                'label' => Yii::t('rbac-admin','Assignment'),
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'assignment'),
                                'url' => ['/rbac/assignment/index'],
                            ],
                        ]
                    ],
                    [
                        'label' => Yii::t('bugTracker', 'Баг-трекер'),
                        'icon' => '<i class="fa fa-tasks"></i>',
                        'url' => '#',
                        'options' => ['class' => 'treeview'],
                        'active' => (\Yii::$app->controller->module->id == 'tracker'),
                        'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_BUGTRACKER),
                        'items' => [
                            [
                                'label' => Yii::t('bugTracker','Проекты'),
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'project'),
                                'url' => ['/tracker/project/index'],
                            ],
                            [   
                                'label' => Yii::t('bugTracker','Задачи'),
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'active' => (\Yii::$app->controller->id == 'task'),
                                'url' => ['/tracker/task/index'],
                            ],
                        ]
                    ],
                    [
                        'label' => Yii::t('view','Forum'),
                        'icon' => '<i class="fa fa-list"></i>',
                        'url' => '#',
                        'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_FORUM),
                        'active' => (\Yii::$app->controller->module->id == 'forum'),
                        'items' => [
                            [
                                'label' => Yii::t('view','Dashboard'),
                                'icon' => '<i class="fa fa-cog"></i>',
                                'active' => (\Yii::$app->controller->id == 'admin' && \Yii::$app->controller->action->id == 'index'),
                                'url' => ['/forum/admin/index'],
                            ],
                            [
                                'label' => Yii::t('view','Forums'),
                                'icon' => '<i class="fa fa-list-ul"></i>',
                                'active' => (\Yii::$app->controller->id == 'admin' && \Yii::$app->controller->action->id == 'categories'),
                                'url' => ['/forum/admin/categories'],
                            ],
                            [
                                'label' => Yii::t('view','Members'),
                                'icon' => '<i class="fa fa-vcard"></i>',
                                'active' => (\Yii::$app->controller->id == 'admin' && \Yii::$app->controller->action->id == 'members'),
                                'url' => ['/forum/admin/members'],
                            ],
                            [
                                'label' => Yii::t('view','Moderators'),
                                'icon' => '<i class="fa fa-user-circle"></i>',
                                'active' => (\Yii::$app->controller->id == 'admin' && \Yii::$app->controller->action->id == 'mods'),
                                'url' => ['/forum/admin/mods'],
                            ],
                            [
                                'label' => Yii::t('view','Contents'),
                                'icon' => '<i class="fa fa-vcard"></i>',
                                'active' => (\Yii::$app->controller->id == 'admin' && \Yii::$app->controller->action->id == 'contents'),
                                'url' => ['/forum/admin/contents'],
                            ],
                            [
                                'label' => Yii::t('view','Settings'),
                                'icon' => '<i class="fa fa-cogs"></i>',
                                'active' => (\Yii::$app->controller->id == 'admin' && \Yii::$app->controller->action->id == 'settings'),
                                'url' => ['/forum/admin/settings'],
                            ],
                            [
                                'label' => Yii::t('view','Logs'),
                                'icon' => '<i class="fa fa-list"></i>',
                                'active' => (\Yii::$app->controller->id == 'admin' && \Yii::$app->controller->action->id == 'logs'),
                                'url' => ['/forum/admin/logs'],
                            ],
                            [
                                'label' => Yii::t('common','Icons'),
                                'icon' => '<i class="fa fa-bookmark"></i>',
                                'active' => (\Yii::$app->controller->id == 'admin' && \Yii::$app->controller->action->id == 'icons'),
                                'url' => ['/forum/admin/icons'],
                            ],
                        ],
                    ],
                    [
                        'label' => Yii::t('backend', 'System'),
                        'options' => ['class' => 'header'],
                        'visible' => (
                                Yii::$app->user->can(User::PERM_ACCESS_TO_STORE) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_USES) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_I18N) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_KEY_VALUE) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_FILE_STORAGE) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_CACHE) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_FILE_MANAGER) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_SYS_INFORMATION) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_LOGS)
                            )
                    ],
                    [
                        'label' => Yii::t('backend', 'Магазин'),
                        'icon' => '<i class="fa fa-shopping-cart"></i>',
                        'url' => ['/shop/index'],
                        'active' => (\Yii::$app->controller->id == 'shop'),
                        'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_STORE)
                    ],
                    [
                        'label' => Yii::t('backend', 'Users'),
                        'icon' => '<i class="fa fa-users"></i>',
                        'url' => ['/user/index'],
                        'active' => (\Yii::$app->controller->id == 'user'),
                        'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_USES)
                    ],
                    [
                        'label' => Yii::t('backend', 'Other'),
                        'url' => '#',
                        'icon' => '<i class="fa fa-cogs"></i>',
                        'options' => ['class' => 'treeview'],
                        'visible' => (
                                Yii::$app->user->can(User::PERM_ACCESS_TO_I18N) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_KEY_VALUE) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_FILE_STORAGE) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_CACHE) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_FILE_MANAGER) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_SYS_INFORMATION) ||
                                Yii::$app->user->can(User::PERM_ACCESS_TO_LOGS)
                            ),
                        'active' => in_array(\Yii::$app->controller->id,['language','key-storage','file-storage','cache','file-manager','system-information', 'log']),
                        'items' => [
                            [
                                'label' => Yii::t('backend', 'i18n'),
                                'url' => '#',
                                'icon' => '<i class="fa fa-flag"></i>',
                                'options' => ['class' => 'treeview'],
                                'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_I18N),
                                'active' => in_array(\Yii::$app->controller->id,['language']),
                                'items' => [
                                    [
                                        'label' => Yii::t('language', 'List of languages'),
                                        'url' => ['/translatemanager/language/list'],
                                        'icon' => '<i class="fa fa-angle-double-right"></i>',
                                    ],
                                    [
                                        'label' => Yii::t('language', 'Import'),
                                        'url' => ['/translatemanager/language/import'],
                                        'icon' => '<i class="fa fa-angle-double-right"></i>',
                                    ],
                                    [
                                        'label' => Yii::t('language', 'Export'),
                                        'url' => ['/translatemanager/language/export'],
                                        'icon' => '<i class="fa fa-angle-double-right"></i>',
                                    ],
                                    [
                                        'label' => Yii::t('language', 'Scan'),
                                        'url' => ['/translatemanager/language/scan'],
                                        'icon' => '<i class="fa fa-angle-double-right"></i>',
                                    ],
                                    [
                                        'label' => Yii::t('language', 'Optimize'),
                                        'url' => ['/translatemanager/language/optimizer'],
                                        'icon' => '<i class="fa fa-angle-double-right"></i>',
                                    ],
                                ]
                            ],
                            [
                                'label' => Yii::t('backend', 'Key-Value Storage'),
                                'url' => ['/key-storage/index'],
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_KEY_VALUE),
                                'active' => (\Yii::$app->controller->id == 'key-storage')
                            ],
                            [
                                'label' => Yii::t('backend', 'File Storage'),
                                'url' => ['/file-storage/index'],
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_FILE_STORAGE),
                                'active' => (\Yii::$app->controller->id == 'file-storage')
                            ],
                            [
                                'label' => Yii::t('backend', 'Cache'),
                                'url' => ['/cache/index'],
                                'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_CACHE),
                                'icon' => '<i class="fa fa-angle-double-right"></i>'
                            ],
                            [
                                'label' => Yii::t('backend', 'File Manager'),
                                'url' => ['/file-manager/index'],
                                'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_FILE_MANAGER),
                                'icon' => '<i class="fa fa-angle-double-right"></i>'
                            ],
                            [
                                'label' => Yii::t('backend', 'System Information'),
                                'url' => ['/system-information/index'],
                                'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_SYS_INFORMATION),
                                'icon' => '<i class="fa fa-angle-double-right"></i>'
                            ],
                            [
                                'label' => Yii::t('backend', 'Logs'),
                                'url' => ['/log/index'],
                                'visible' => Yii::$app->user->can(User::PERM_ACCESS_TO_LOGS),
                                'icon' => '<i class="fa fa-angle-double-right"></i>',
                                'badge' => SystemLog::find()->count(),
                                'badgeBgClass' => 'label-danger',
                            ],
                        ]
                    ]
                ]
            ]) ?>
        </section>
        <!-- /.sidebar -->
    </aside>

    <!-- Right side column. Contains the navbar and content of the page -->
    <aside class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h1>
                <?php echo $this->title ?>
                <?php if (isset($this->params['subtitle'])): ?>
                    <small><?php echo $this->params['subtitle'] ?></small>
                <?php endif; ?>
            </h1>

            <?php echo Breadcrumbs::widget([
                'tag' => 'ol',
                'links' => isset(Yii::$app->params['breadcrumbs']) ? Yii::$app->params['breadcrumbs'] : [],
            ]) ?>
        </section>

        <!-- Main content -->
        <section class="content">
            
            <?= Alert::widget() ?>
            
            <?php echo $content ?>
        </section><!-- /.content -->
    </aside><!-- /.right-side -->
</div><!-- ./wrapper -->

<?php $this->endContent(); ?>
