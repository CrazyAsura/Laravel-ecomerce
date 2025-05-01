<?php $__env->startSection('title', 'Produtos'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Nossos Produtos</h2>
        </div>
        <div class="col-md-6 text-end">
            <form action="<?php echo e(route('public.produtos.index')); ?>" method="GET">
                <div class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Buscar produtos..." value="<?php echo e(request('search')); ?>">
                    <button class="btn btn-outline-secondary" type="submit">
                        <i class="fas fa-search"></i>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <div class="row">
        <?php $__empty_1 = true; $__currentLoopData = $produtos; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <img src="<?php echo e(asset($produto->imagem ? 'storage/'.$produto->imagem : 'images/default-product.png')); ?>"
                     class="card-img-top"
                     alt="<?php echo e($produto->nome); ?>"
                     style="height: 200px; object-fit: cover;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo e($produto->nome); ?></h5>
                    <p class="card-text"><?php echo e(Str::limit($produto->descricao, 100)); ?></p>
                    <p class="h5 text-primary"><?php echo e($produto->formatted_price); ?></p>
                    <?php if($produto->quantidade > 0): ?>
                        <span class="badge bg-success">Em estoque</span>
                    <?php else: ?>
                        <span class="badge bg-danger">Esgotado</span>
                    <?php endif; ?>
                </div>
                <div class="card-footer bg-white">
                    <a href="<?php echo e(route('produtos.show', $produto)); ?>" class="btn btn-outline-primary btn-sm">
                        Ver detalhes
                    </a>
                    <?php if(auth()->guard()->check()): ?>
                    <form action="<?php echo e(route('cart.store', $produto)); ?>" method="POST" class="d-inline">
                        <?php echo csrf_field(); ?>
                        <input type="hidden" name="quantity" value="1">
                        <button type="submit" class="btn btn-primary btn-sm"
                                <?php echo e($produto->quantidade === 0 ? 'disabled' : ''); ?>>
                            <i class="fas fa-cart-plus"></i> Comprar
                        </button>
                    </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <div class="col-12">
            <div class="alert alert-info">Nenhum produto encontrado.</div>
        </div>
        <?php endif; ?>
    </div>

    <?php echo e($produtos->links()); ?>

</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecomerce\Projeto-1\Nestfy\resources\views/produtos/index.blade.php ENDPATH**/ ?>