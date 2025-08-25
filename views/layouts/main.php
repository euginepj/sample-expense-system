<?php

/** @var yii\web\View $this */
/** @var string $content */

use app\assets\AppAsset;
use app\widgets\Alert;
use yii\bootstrap5\Breadcrumbs;
use yii\bootstrap5\Html;
use yii\bootstrap5\Nav;
use yii\bootstrap5\NavBar;

AppAsset::register($this);

$this->registerCsrfMetaTags();
$this->registerMetaTag(['charset' => Yii::$app->charset], 'charset');
$this->registerMetaTag(['name' => 'viewport', 'content' => 'width=device-width, initial-scale=1, shrink-to-fit=no']);
$this->registerMetaTag(['name' => 'description', 'content' => $this->params['meta_description'] ?? '']);
$this->registerMetaTag(['name' => 'keywords', 'content' => $this->params['meta_keywords'] ?? '']);
$this->registerLinkTag(['rel' => 'icon', 'type' => 'image/x-icon', 'href' => Yii::getAlias('@web/favicon.ico')]);

// Check if user is admin for styling
$isAdmin = !Yii::$app->user->isGuest && Yii::$app->user->identity->role === 'admin';
$bodyClass = $isAdmin ? 'admin-theme' : 'user-theme';
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" class="h-100">
<head>
    <title><?= Html::encode($this->title) ?></title>
    <?php $this->head() ?>
    <style>
        /* Admin Theme - Professional Blue */
        .admin-theme {
            background-color: #f8f9fa;
        }
        .admin-theme .navbar-dark {
            background: linear-gradient(135deg, #2c3e50 0%, #3498db 100%) !important;
        }
        .admin-theme .btn-primary {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
        }
        .admin-theme .btn-success {
            background: linear-gradient(135deg, #27ae60 0%, #229954 100%);
            border: none;
        }
        .admin-theme .btn-danger {
            background: linear-gradient(135deg, #e74c3c 0%, #c0392b 100%);
            border: none;
        }
        .admin-theme .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            border: none;
            border-radius: 10px;
        }
        .admin-theme .navbar-brand {
            font-weight: bold;
            font-size: 1.5rem;
        }

        /* User Theme - Friendly Green */
        .user-theme {
            background-color: #f0f8f0;
        }
        .user-theme .navbar-dark {
            background: linear-gradient(135deg, #0c6330ff 0%, #367852ff 100%) !important;
        }
        .user-theme .btn-primary {
            background: linear-gradient(135deg, #1e8147ff 0%, #20894cff 100%);
            border: none;
        }
        .user-theme .btn-success {
            background: linear-gradient(135deg, #3498db 0%, #2980b9 100%);
            border: none;
        }
        .user-theme .card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            border: none;
            border-radius: 8px;
        }

        /* Common Styles */
        .navbar-brand {
            font-weight: bold;
        }
        .main-container {
            min-height: calc(100vh - 120px);
        }
        .status-pending { background-color: #fff3cd; color: #856404; }
        .status-approved { background-color: #d4edda; color: #155724; }
        .status-rejected { background-color: #f8d7da; color: #721c24; }
        .badge-status {
            padding: 0.5em 0.8em;
            border-radius: 15px;
            font-weight: 500;
        }
        .expense-card {
            transition: transform 0.2s ease;
        }
        .expense-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15) !important;
        }
    </style>
</head>
<body class="d-flex flex-column h-100 <?= $bodyClass ?>">
<?php $this->beginBody() ?>

<header id="header">
    <?php
    $navItems = [
        ['label' => 'Home', 'url' => ['/site/index']],
        ['label' => 'My Expenses', 'url' => ['/expense/index'], 'visible' => !Yii::$app->user->isGuest],
        ['label' => 'Submit Expense', 'url' => ['/expense/create'], 'visible' => !Yii::$app->user->isGuest],
    ];

    // Add Admin Review link only for admins
    if ($isAdmin) {
        $navItems[] = ['label' => 'Admin Review', 'url' => ['/admin/index'], 'visible' => !Yii::$app->user->isGuest];
    }

    $navItems[] = Yii::$app->user->isGuest
        ? ['label' => 'Login', 'url' => ['/site/login']]
        : '<li class="nav-item">'
            . Html::beginForm(['/site/logout'])
            . Html::submitButton(
                'Logout (' . Yii::$app->user->identity->username . ')',
                ['class' => 'nav-link btn btn-link logout']
            )
            . Html::endForm()
            . '</li>';

    NavBar::begin([
        'brandLabel' => Html::tag('span', Yii::$app->name) . 
                       ($isAdmin ? Html::tag('small', ' Admin Panel', ['class' => 'ms-2 badge bg-light text-dark']) : ''),
        'brandUrl' => Yii::$app->homeUrl,
        'options' => ['class' => 'navbar-expand-md navbar-dark fixed-top shadow']
    ]);
    
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav ms-auto'],
        'items' => $navItems
    ]);
    
    NavBar::end();
    ?>
</header>

<main id="main" class="flex-shrink-0 main-container" role="main">
    <div class="container">
        <?php if (!empty($this->params['breadcrumbs'])): ?>
            <?= Breadcrumbs::widget([
                'links' => $this->params['breadcrumbs'],
                'options' => ['class' => 'breadcrumb mb-4 p-3 bg-light rounded']
            ]) ?>
        <?php endif ?>
        
        <?= Alert::widget() ?>
        
        <div class="content-wrapper">
            <?= $content ?>
        </div>
    </div>
</main>

<footer id="footer" class="mt-auto py-3 <?= $isAdmin ? 'bg-dark text-light' : 'bg-light' ?>">
    <div class="container">
        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                &copy; Expense System <?= date('Y') ?>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <small><?= Yii::powered() ?></small>
            </div>
        </div>
    </div>
</footer>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>