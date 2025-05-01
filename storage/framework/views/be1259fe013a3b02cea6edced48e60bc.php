<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?php echo e(route('home')); ?>"><?php echo e(config('app.name')); ?></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('public.produtos.index')); ?>">Produtos</a>
                </li>
            </ul>

            <ul class="navbar-nav">
                <?php if(auth()->guard()->check()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('public.cart.index')); ?>">
                        <i class="fas fa-shopping-cart"></i>
                        <?php if(Auth::user()->cartItems->count() > 0): ?>
                            <span class="badge bg-danger"><?php echo e(Auth::user()->cartItems->count()); ?></span>
                        <?php endif; ?>
                    </a>
                </li>

                <?php if(auth()->user()->isAdmin()): ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('admin.dashboard')); ?>">
                        <i class="fas fa-cog"></i> Admin
                    </a>
                </li>
                <?php endif; ?>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <?php echo e(Auth::user()->name); ?>

                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="<?php echo e(route('admin.orders.index')); ?>">Meus Pedidos</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <button type="submit" class="dropdown-item">Sair</button>
                            </form>
                        </li>
                    </ul>
                </li>
                <?php else: ?>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('login')); ?>">Login</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?php echo e(route('register')); ?>">Cadastre-se</a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>
<?php /**PATH C:\xampp\htdocs\ecomerce\Projeto-1\Nestfy\resources\views/layouts/partials/navbar.blade.php ENDPATH**/ ?>