<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Lista de Pedidos</div>

                <div class="card-body">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Cliente</th>
                                <th>Status</th>
                                <th>Total</th>
                                <th>Data</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <tr>
                                    <td><?php echo e($order->id); ?></td>
                                    <td><?php echo e($order->user->name); ?></td>
                                    <td>
                                        <span class="badge bg-<?php echo e($order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'danger')); ?>">
                                            <?php echo e($order->status); ?>

                                        </span>
                                    </td>
                                    <td>R$ <?php echo e(number_format($order->total, 2, ',', '.')); ?></td>
                                    <td><?php echo e($order->created_at->format('d/m/Y H:i')); ?></td>
                                    <td>
                                        <a href="<?php echo e(route('admin.orders.show', $order)); ?>" class="btn btn-sm btn-info">
                                            Detalhes
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </tbody>
                    </table>

                    <?php echo e($orders->links()); ?>

                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecomerce\Projeto-1\Nestfy\resources\views/admin/orders/index.blade.php ENDPATH**/ ?>