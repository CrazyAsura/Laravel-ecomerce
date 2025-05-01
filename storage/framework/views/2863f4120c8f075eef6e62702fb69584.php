<?php $__env->startSection('title', 'Carrinho de Compras'); ?>

<?php $__env->startSection('content'); ?>
<div class="container py-4">
    <h2 class="mb-4">Seu Carrinho</h2>

    <?php if($cartItems->isEmpty()): ?>
        <div class="alert alert-info">
            Seu carrinho está vazio. <a href="<?php echo e(route('public.produtos.index')); ?>">Continue comprando</a>
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Produto</th>
                        <th>Preço</th>
                        <th>Quantidade</th>
                        <th>Total</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $__currentLoopData = $cartItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $cart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <img src="<?php echo e(asset($cart->product->imagem ? 'storage/'.$cart->product->imagem : 'images/default-product.png')); ?>"
                                     alt="<?php echo e($cart->product->nome); ?>"
                                     width="60" class="me-3">
                                <div>
                                    <h5><?php echo e($cart->product->nome); ?></h5>
                                    <p class="text-muted mb-0"><?php echo e($cart->product->categoria->nome ?? ''); ?></p>
                                </div>
                            </div>
                        </td>
                        <td><?php echo e($cart->product->formatted_price); ?></td>
                        <td>
                            <form action="<?php echo e(route('public.cart.update', $cart)); ?>" method="POST" class="d-flex">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('PUT'); ?>
                                <input type="number" name="quantity" value="<?php echo e($cart->quantity); ?>"
                                       min="1" max="<?php echo e($cart->product->quantidade); ?>" class="form-control" style="width: 70px;">
                                <button type="submit" class="btn btn-sm btn-outline-primary ms-2">
                                    <i class="fas fa-sync-alt"></i>
                                </button>
                            </form>
                        </td>
                        <td><?php echo e($cart->formatted_total); ?></td>
                        <td>
                            <form action="<?php echo e(route('public.cart.remove', $cart)); ?>" method="POST">
                                <?php echo csrf_field(); ?>
                                <?php echo method_field('DELETE'); ?>
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="3" class="text-end"><strong>Total:</strong></td>
                        <td><strong>R$ <?php echo e(number_format($total, 2, ',', '.')); ?></strong></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="d-flex justify-content-between mt-4">
            <a href="<?php echo e(route('public.produtos.index')); ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-2"></i> Continuar Comprando
            </a>
            <a href="<?php echo e(route('checkout')); ?>" class="btn btn-primary">
                Finalizar Compra <i class="fas fa-arrow-right ms-2"></i>
            </a>
        </div>
    <?php endif; ?>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecomerce\Projeto-1\Nestfy\resources\views/cart/index.blade.php ENDPATH**/ ?>