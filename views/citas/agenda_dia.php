<?php
$page_title = "Agenda del Día";
$page_css = "citas.css";

require_once '../../includes/header.php';
require_once '../../includes/sidebar.php';
require_once '../../models/Cita.php';
require_once '../../models/Odontologo.php';

$citaModel = new Cita();
$odoModel = new Odontologo();
$doctores = $odoModel->listarTodos();

// Fecha y filtro
$fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$id_odontologo = isset($_GET['id_odontologo']) && !empty($_GET['id_odontologo']) ? $_GET['id_odontologo'] : null;

// Obtener citas
$citas = $citaModel->obtenerCitasDelDia($fecha, $id_odontologo);
?>

<main class="main-content">
    <div class="page-header">
        <h1><i class="fas fa-calendar-day"></i> Agenda del Día</h1>

        <div style="display: flex; gap: 10px;">
            <a href="calendario.php" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="far fa-calendar-alt"></i> Ver Calendario
            </a>

            <a href="nueva.php" class="btn-primary" style="text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                <i class="fas fa-plus"></i> Nueva Cita
            </a>
        </div>
    </div>

    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); margin-bottom: 20px;">
        <form action="" method="GET" style="display: flex; gap: 15px; align-items: flex-end; flex-wrap: wrap;">
            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label>Fecha:</label>
                <input type="date" name="fecha" class="form-control" value="<?php echo $fecha; ?>" required>
            </div>

            <div class="form-group" style="flex: 1; min-width: 200px;">
                <label>Doctor:</label>
                <select name="id_odontologo" class="form-control">
                    <option value="">Todos los doctores</option>
                    <?php foreach ($doctores as $d): ?>
                        <option value="<?php echo $d['id_odontologo']; ?>"
                            <?php echo ($id_odontologo == $d['id_odontologo']) ? 'selected' : ''; ?>>
                            Dr. <?php echo $d['nombres'] . ' ' . $d['apellidos']; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn-primary" style="height: 42px;">
                <i class="fas fa-search"></i> Buscar
            </button>
        </form>
    </div>

    <div style="background: white; border-radius: 8px; box-shadow: 0 2px 5px rgba(0,0,0,0.05); overflow: hidden;">
        <div style="background: #34495e; color: white; padding: 15px; display: flex; justify-content: space-between; align-items: center;">
            <h3 style="margin: 0;">
                <i class="far fa-clock"></i>
                <?php
                setlocale(LC_TIME, 'es_ES.UTF-8', 'es_ES', 'Spanish_Spain');
                echo strftime('%A, %d de %B de %Y', strtotime($fecha));
                ?>
            </h3>
            <span style="background: rgba(255,255,255,0.2); padding: 5px 15px; border-radius: 20px;">
                <?php echo count($citas); ?> cita<?php echo count($citas) != 1 ? 's' : ''; ?>
            </span>
        </div>

        <?php if (empty($citas)): ?>
            <div style="padding: 60px 20px; text-align: center; color: #999;">
                <i class="far fa-calendar-times" style="font-size: 4rem; margin-bottom: 15px; opacity: 0.3;"></i>
                <p style="font-size: 1.1rem;">No hay citas programadas para este día</p>
            </div>
        <?php else: ?>
            <div style="padding: 0;">
                <?php foreach ($citas as $c):
                    $hora_inicio = date('H:i', strtotime($c['fecha_hora_inicio']));
                    $hora_fin = date('H:i', strtotime($c['fecha_hora_fin']));

                    // Colores según estado
                    $color_borde = '#3498db'; // PROGRAMADA
                    $color_fondo = '#e3f2fd';
                    $icono_estado = 'fa-clock';

                    if ($c['estado'] == 'ATENDIDA') {
                        $color_borde = '#2ecc71';
                        $color_fondo = '#e8f5e9';
                        $icono_estado = 'fa-check-circle';
                    } elseif ($c['estado'] == 'CANCELADA') {
                        $color_borde = '#e74c3c';
                        $color_fondo = '#ffebee';
                        $icono_estado = 'fa-ban';
                    } elseif ($c['estado'] == 'NO_ASISTIO') {
                        $color_borde = '#95a5a6';
                        $color_fondo = '#f5f5f5';
                        $icono_estado = 'fa-user-times';
                    }
                ?>
                    <div style="border-left: 5px solid <?php echo $color_borde; ?>; 
                            background: <?php echo $color_fondo; ?>; 
                            padding: 20px; 
                            margin: 0; 
                            border-bottom: 1px solid #eee;
                            display: flex;
                            gap: 20px;
                            align-items: center;">

                        <div style="min-width: 100px; text-align: center;">
                            <div style="font-size: 1.5rem; font-weight: bold; color: <?php echo $color_borde; ?>;">
                                <?php echo $hora_inicio; ?>
                            </div>
                            <div style="font-size: 0.8rem; color: #666;">
                                <?php echo $hora_fin; ?>
                            </div>
                        </div>

                        <div style="flex: 1;">
                            <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 5px;">
                                <h4 style="margin: 0; font-size: 1.1rem; color: #2c3e50;">
                                    <?php echo $c['paciente']; ?>
                                </h4>
                                <span style="background: white; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; color: #666; border: 1px solid #eee;">
                                    CI: <?php echo $c['ci']; ?> 
                                    <?php echo (!empty($c['departamento'])) ? ' (' . $c['departamento'] . ')' : ''; ?>
                                </span>
                            </div>

                            <p style="margin: 5px 0; color: #555; font-size: 0.9rem;">
                                <i class="fas fa-stethoscope"></i> <strong><?php echo $c['odontologo']; ?></strong>
                            </p>

                            <p style="margin: 5px 0; color: #666; font-size: 0.9rem;">
                                <i class="fas fa-comment-medical"></i> <?php echo $c['motivo']; ?>
                            </p>

                            <?php if ($c['telefono']): ?>
                                <p style="margin: 5px 0; color: #666; font-size: 0.85rem;">
                                    <i class="fas fa-phone"></i> <?php echo $c['telefono']; ?>
                                </p>
                            <?php endif; ?>
                        </div>

                        <div style="text-align: right; display: flex; flex-direction: column; gap: 8px; align-items: flex-end;">
                            <span style="background: <?php echo $color_borde; ?>; 
                                     color: white; 
                                     padding: 5px 15px; 
                                     border-radius: 20px; 
                                     font-size: 0.85rem;
                                     display: inline-flex;
                                     align-items: center;
                                     gap: 5px;">
                                <i class="fas <?php echo $icono_estado; ?>"></i>
                                <?php echo $c['estado']; ?>
                            </span>

                            <div style="display: flex; gap: 5px;">
                                <div style="display: flex; gap: 5px;">
                                    <?php if ($c['estado'] == 'PROGRAMADA'): ?>
                                        <span style="background: #2ecc71; color: white; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px; cursor: default;">
                                            <i class="fas fa-user-md"></i> Atender
                                        </span>
                                    <?php endif; ?>

                                    <span style="background: #34495e; color: white; padding: 6px 12px; border-radius: 4px; font-size: 0.85rem; display: inline-flex; align-items: center; gap: 5px; cursor: default;">
                                        <i class="fas fa-file-medical"></i> Historial
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <div style="margin-top: 20px; text-align: center;">
        <button onclick="window.print()" style="background: #7f8c8d; color: white; border: none; padding: 10px 25px; border-radius: 5px; cursor: pointer; font-size: 0.95rem;">
            <i class="fas fa-print"></i> Imprimir Agenda
        </button>
    </div>
</main>

<style>
    @media print {

        .sidebar,
        .top-bar,
        .btn-primary,
        button,
        .page-header a {
            display: none !important;
        }

        .main-content {
            padding: 0;
            background: white;
        }

        body {
            background: white;
        }
    }
</style>

<?php require_once '../../includes/footer.php'; ?>