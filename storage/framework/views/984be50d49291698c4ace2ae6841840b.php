<?php $__env->startSection('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">Dashboard</div>

                <div class="card-body">
                    <!-- Ações Rápidas -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Ações Rápidas</h5>
                                    <div class="d-grid gap-2">
                                        <a href="<?php echo e(route('admin.produtos.create')); ?>" class="btn btn-primary">
                                            <i class="fas fa-plus"></i> Novo Produto
                                        </a>
                                        <a href="<?php echo e(route('admin.categorias.create')); ?>" class="btn btn-success">
                                            <i class="fas fa-plus"></i> Nova Categoria
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title">Estatísticas</h5>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="small text-muted">Total de Produtos</div>
                                            <div class="h4"><?php echo e($totalProdutos); ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-muted">Total de Categorias</div>
                                            <div class="h4"><?php echo e($totalCategorias); ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-muted">Total de Pedidos</div>
                                            <div class="h4"><?php echo e($totalPedidos); ?></div>
                                        </div>
                                        <div class="col-6">
                                            <div class="small text-muted">Total de Usuários</div>
                                            <div class="h4"><?php echo e($totalUsuarios); ?></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Produtos Recentes -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Produtos Recentes</span>
                                    <a href="<?php echo e(route('admin.produtos.index')); ?>" class="btn btn-sm btn-primary">
                                        Ver Todos
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Categoria</th>
                                                    <th>Preço</th>
                                                    <th>Estoque</th>
                                                    <th>Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $produtosRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $produto): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($produto->nome); ?></td>
                                                        <td><?php echo e($produto->categoria->nome); ?></td>
                                                        <td>R$ <?php echo e(number_format($produto->preco, 2, ',', '.')); ?></td>
                                                        <td><?php echo e($produto->estoque); ?></td>
                                                        <td><?php echo e($produto->created_at->format('d/m/Y H:i')); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Categorias Recentes -->
                    <div class="row mb-4">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Categorias Recentes</span>
                                    <a href="<?php echo e(route('admin.categorias.index')); ?>" class="btn btn-sm btn-primary">
                                        Ver Todas
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>Nome</th>
                                                    <th>Produtos</th>
                                                    <th>Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $categoriasRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $categoria): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($categoria->nome); ?></td>
                                                        <td><?php echo e($categoria->produtos_count); ?></td>
                                                        <td><?php echo e($categoria->created_at->format('d/m/Y H:i')); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Pedidos Recentes -->
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <span>Pedidos Recentes</span>
                                    <a href="<?php echo e(route('admin.orders.index')); ?>" class="btn btn-sm btn-primary">
                                        Ver Todos
                                    </a>
                                </div>
                                <div class="card-body">
                                    <div class="table-responsive">
                                        <table class="table">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Cliente</th>
                                                    <th>Total</th>
                                                    <th>Status</th>
                                                    <th>Data</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php $__currentLoopData = $pedidosRecentes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pedido): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <tr>
                                                        <td><?php echo e($pedido->id); ?></td>
                                                        <td><?php echo e($pedido->user->name); ?></td>
                                                        <td>R$ <?php echo e(number_format($pedido->total, 2, ',', '.')); ?></td>
                                                        <td>
                                                            <span class="badge bg-<?php echo e($pedido->status === 'completed' ? 'success' : ($pedido->status === 'pending' ? 'warning' : 'danger')); ?>">
                                                                <?php echo e(ucfirst($pedido->status)); ?>

                                                            </span>
                                                        </td>
                                                        <td><?php echo e($pedido->created_at->format('d/m/Y H:i')); ?></td>
                                                    </tr>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\xampp\htdocs\ecomerce\Projeto-1\Nestfy\resources\views/admin/dashboard.blade.php ENDPATH**/ ?>