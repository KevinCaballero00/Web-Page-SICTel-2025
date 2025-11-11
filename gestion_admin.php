<?php
require_once 'config.php';
session_start();

// Verificar si el usuario tiene permisos de administrador
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'Administrador') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Acceso denegado.';
    exit;
}

// Eliminar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete_user') {
    $id = intval($_POST['id']);
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->close();
    header('Location: gestion_admin.php');
    exit;
}

// Editar usuario
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'edit_user') {
    $id = intval($_POST['id']);
    $name = $_POST['name'];
    $lastName = $_POST['lastName'];
    $email = $_POST['email'];
    $role = $_POST['role'];

    $stmt = $conn->prepare("UPDATE users SET name = ?, lastName = ?, email = ?, role = ? WHERE id = ?");
    $stmt->bind_param('ssssi', $name, $lastName, $email, $role, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: gestion_admin.php');
    exit;
}

// Obtener lista de usuarios
$result = $conn->query("SELECT id, name, lastName, email, role FROM users ORDER BY id DESC");

// --- NUEVO: asignar ponencia a evaluador ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'assign_ponencia') {
    $form_id = intval($_POST['form_id'] ?? 0);
    $evaluador_id = intval($_POST['evaluador_id'] ?? 0);

    if ($form_id > 0) {
        if ($evaluador_id > 0) {
            $stmt = $conn->prepare("UPDATE formularios SET evaluador_id = ? WHERE id = ?");
            $stmt->bind_param('ii', $evaluador_id, $form_id);
        } else {
            // desasignar
            $stmt = $conn->prepare("UPDATE formularios SET evaluador_id = NULL WHERE id = ?");
            $stmt->bind_param('i', $form_id);
        }
        $stmt->execute();
        $stmt->close();
    }
    header('Location: gestion_admin.php');
    exit;
}

// Obtener lista de evaluadores para los selects (solo role = Evaluador)
    $evaluadores = $conn->query("SELECT id, name, lastName, email FROM users WHERE role = 'Evaluador' ORDER BY name");

// Obtener lista de ponencias (añadido evaluador_id)
    $forms_result = $conn->query("SELECT id, name_form, institu, modalidad, programa, estado, eval_status, revisado, evaluador_id FROM formularios ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Usuarios por Administrador</title>
    <meta name="author" content="SICTeI">
    <meta name="description" content="SICTeI - Semana Internacional de Ciencia Tecnología e Innovacción">
    <meta name="keywords" content="SICTeI">
    <meta name="robots" content="INDEX,FOLLOW">

    <!-- Mobile Specific Metas -->
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Favicons - Place favicon.ico in the root directory -->

    <link rel="icon" href="assets/img/favicons/FaviconSICTeI2024.jpg" type="image/jpeg">

    <link rel="manifest" href="assets/img/favicons/manifest.json">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-TileImage" content="assets/img/favicons/ms-icon-144x144.png">
    <meta name="theme-color" content="#ffffff">

    <!--==============================
	  Google Fonts
	============================== -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=DM+Sans:opsz,wght@9..40,300;9..40,400;9..40,500;9..40,600;9..40,700;9..40,800&family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!--==============================
	    All CSS File
	============================== -->
    <!-- Bootstrap -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css">
    <!-- Fontawesome Icon -->
    <link rel="stylesheet" href="assets/css/fontawesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <!-- Magnific Popup -->
    <link rel="stylesheet" href="assets/css/magnific-popup.min.css">
    <!-- Swiper Js -->
    <link rel="stylesheet" href="assets/css/swiper-bundle.min.css">
    <!-- Theme Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
    <!-- gestion CSS -->
    <link rel="stylesheet" href="assets/css/styleGestionAdmin.css">

</head>

<body>
    <!--============================== Mobile Menu ============================== -->
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle"><i class="fas fa-times"></i></button>
            <div class="mobile-logo col-lg-3">
                <a href="index.html"><img src="assets/img/img/Y.png" alt="SICTeI"></a>
            </div>
            <div class="th-mobile-menu">
                <ul>
                    <li>
                        <a href="index.html">INICIO</a>
                    </li>
                    <li>
                        <a href="calendario.html">CALENDARIO</a>
                    </li>
                    <li>
                        <a href="comites.html">COMITES</a>
                    </li>

                    <li>
                        <a href="contact.html">CONTACTO</a>
                    </li>


                    <li class="menu-item-has-children">
                        <a href="#">PUBLICACIONES</a>
                        <ul class="sub-menu">
                            <li><a href="revistas.html">Revistas</a></li>
                            <li><a href="memorias.html">Memorias</a></li>
                            <li><a href="resultados.html">Resultados</a></li>
                            <li><a
                                    href="https://drive.google.com/drive/folders/1YlH2FJZvXLA7IUe_Eu5gvRkIZQFxSn82?usp=sharing">Posters</a>
                            </li>
                            <li><a href="contenido.html">Contenido</a></li>
                        </ul>
                    </li>
                    <li class="menu-item-has-children">
                        <a href="#">EDICIONES</a>
                        <ul class="sub-menu">
                            <li><a href="http://www.ufps.edu.co/ufps/IIIsemanainternacional/">3ra Edicion</a></li>
                            <li><a href="http://www.ufps.edu.co/ufps/IVsemanainternacional/index.html">4ta Edicion</a>
                            </li>
                            <li><a href="https://seincienciatecnologia.ufpso.edu.co/2018/index.html">5ta Edicion</a>
                            </li>
                            <li><a href="https://seincienciatecnologia.ufpso.edu.co/2019/index.html">6ta Edicion</a>
                            </li>
                            <li><a href="https://seincienciatecnologia.ufpso.edu.co/2020/index.html">7ma Edicion</a>
                            </li>
                            <li><a href="https://foristom.org/8iwsti/index.html">8va Edicion</a></li>
                            <li><a href="https://seincienciatecnologia.ufpso.edu.co/2022/index.html">9na Edicion</a>
                            </li>
                            <li><a href="https://seincienciatecnologia.ufpso.edu.co">10ma Edicion</a></li>
                            <li><a href="https://sictei.ufps.edu.co/index.html"></a>11va Edicion</li>

                        </ul>
                    </li>

                    <li>
                        <a href="perfil.php">USUARIO</a>
                    </li>

                    <li>
                        <a href="login.php">LOGIN</a>

                    </li>
                </ul>
            </div>
        </div>
    </div>


    <!--============================== Header Area ==============================-->
    <nav class="navbar navbar-expand-lg navbar-custom ">
        <div class="container-fluid">
            <!-- Logo and Title -->
            <a class="navbar-brand text-white" href="index.html">
                <img src="assets/img/img/Y.png" alt="Logo" width="270">
            </a>

            <!-- Navbar Toggler for mobile view -->
            <button class="th-menu-toggle d-block d-lg-none" type="button">
                <i class="far fa-bars"></i>
            </button>

            <!-- Links -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto main-menu">
                    <li class="nav-item">
                        <a class="nav-link" href="index.html">INICIO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="calendario.html">CALENDARIO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="comites.html">COMITES</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="contact.html">CONTACTO</a>
                    </li>

                    <li class="nav-item dropdown menu-item-has-children">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"> PUBLICACIONES </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="revistas.html">Revistas</a></li>
                            <li><a class="dropdown-item" href="memorias.html">Memorias</a></li>
                            <li><a class="dropdown-item" href="resultados.html">Resultados</a></li>
                            <li><a class="dropdown-item" href="https://drive.google.com/drive/folders/1YlH2FJZvXLA7IUe_Eu5gvRkIZQFxSn82?usp=sharing">Posters</a> </li>
                            <li><a class="dropdown-item" href="contenido.html">Contenido</a></li>
                        </ul>
                    </li>
                    <li class="nav-item dropdown menu-item-has-children">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false"> EDICIONES </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="http://www.ufps.edu.co/ufps/IIIsemanainternacional/"
                                    target="_blank">3ra Edicion</a>
                            <li><a class="dropdown-item" href="http://www.ufps.edu.co/ufps/IVsemanainternacional/index.html"
                                    target="_blank">4ta Edicion</a></li>
                            <li><a class="dropdown-item" href="https://seincienciatecnologia.ufpso.edu.co/2018/index.html"
                                    target="_blank">5ta Edicion</a></li>
                            <li><a class="dropdown-item" href="https://seincienciatecnologia.ufpso.edu.co/2019/index.html"
                                    target="_blank">6ta Edicion</a></li>
                            <li><a class="dropdown-item" href="https://seincienciatecnologia.ufpso.edu.co/2020/index.html"
                                    target="_blank">7ma Edicion</a></li>
                            <li><a class="dropdown-item" href="https://foristom.org/8iwsti/index.html" target="_blank">8va
                                    Edicion</a></li>
                            <li><a class="dropdown-item" href="https://seincienciatecnologia.ufpso.edu.co/2022/index.html"
                                    target="_blank">9na Edicion</a></li>
                            <li><a class="dropdown-item" href="https://seincienciatecnologia.ufpso.edu.co"
                                    target="_blank">10ma Edicion</a></li>
                            <li><a class="dropdown-item" href="https://sictei.ufps.edu.co/index.html"
                                    target="_blank">11va Edicion</a></li>
                        </ul>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="perfil.php">USUARIO</a>
                    </li>

                </ul>

                <a href="login.php" class="btn btn-inscripcion ms-2 me-3">LOGIN</a>
            </div>
        </div>
    </nav>

    <!--============================== Gestion Area ==============================-->

    <div class="container mt-5">
        <h2 class="text-center">Gestión de Usuarios</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nombre</th>
                    <th>Apellido</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $row['id']; ?></td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['lastName']); ?></td>
                        <td><?php echo htmlspecialchars($row['email']); ?></td>
                        <td><?php echo htmlspecialchars($row['role']); ?></td>
                        <td>
                            <!-- Botón para editar -->
                            <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?php echo $row['id']; ?>">Editar</button>

                            <!-- Botón para eliminar -->
                            <form method="post" action="gestion_admin.php" style="display:inline;">
                                <input type="hidden" name="action" value="delete_user">
                                <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</button>
                            </form>
                        </td>
                    </tr>

                    <!-- Modal para editar usuario -->
                    <div class="modal fade" id="editModal<?php echo $row['id']; ?>" tabindex="-1" aria-labelledby="editModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <form method="post" action="gestion_admin.php">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="editModalLabel<?php echo $row['id']; ?>">Editar Usuario</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <input type="hidden" name="action" value="edit_user">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <div class="mb-3">
                                            <label for="name<?php echo $row['id']; ?>" class="form-label">Nombre</label>
                                            <input type="text" class="form-control" id="name<?php echo $row['id']; ?>" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="lastName<?php echo $row['id']; ?>" class="form-label">Apellido</label>
                                            <input type="text" class="form-control" id="lastName<?php echo $row['id']; ?>" name="lastName" value="<?php echo htmlspecialchars($row['lastName']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="email<?php echo $row['id']; ?>" class="form-label">Email</label>
                                            <input type="email" class="form-control" id="email<?php echo $row['id']; ?>" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                        </div>
                                        <div class="mb-3">
                                            <label for="role<?php echo $row['id']; ?>" class="form-label">Rol</label>
                                            <select class="form-select" id="role<?php echo $row['id']; ?>" name="role" required>
                                                <option value="Ponente" <?php if ($row['role'] === 'Ponente') echo 'selected'; ?>>Ponente</option>
                                                <option value="Evaluador" <?php if ($row['role'] === 'Evaluador') echo 'selected'; ?>>Evaluador</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- NUEVA SECCIÓN: Asignar ponencias a evaluadores -->
    <div class="container mt-5">
        <h2 class="text-center">Asignar Ponencias a Evaluadores</h2>
        <table class="table table-bordered mt-4">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Título</th>
                    <th>Institución</th>
                    <th>Modalidad</th>
                    <th>Evaluador asignado</th>
                    <th>Asignar Evaluador</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($f = $forms_result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo $f['id']; ?></td>
                        <td><?php echo htmlspecialchars($f['name_form']); ?></td>
                        <td><?php echo htmlspecialchars($f['institu']); ?></td>
                        <td><?php echo htmlspecialchars($f['modalidad']); ?></td>
                        <td>
                            <?php
                                $assigned_name = 'Sin asignar';
                                if (!empty($f['evaluador_id'])) {
                                    // buscar el nombre del evaluador (puede mejorarse con JOIN en la query)
                                    $qid = intval($f['evaluador_id']);
                                    $r = $conn->query("SELECT name, lastName FROM users WHERE id = $qid LIMIT 1");
                                    if ($rr = $r->fetch_assoc()) {
                                        $assigned_name = htmlspecialchars($rr['name'].' '.$rr['lastName']);
                                    }
                                }
                                echo $assigned_name;
                            ?>
                        </td>
                        <td>
                            <form method="post" action="gestion_admin.php" class="d-flex align-items-center">
                                <input type="hidden" name="action" value="assign_ponencia">
                                <input type="hidden" name="form_id" value="<?php echo $f['id']; ?>">
                                <select name="evaluador_id" class="form-select me-2" style="width:auto">
                                    <option value="0">-- Sin asignar --</option>
                                    <?php if ($evaluadores): ?>
                                        <?php
                                            // reset pointer si ya fue leída anteriormente
                                            $evaluadores->data_seek(0);
                                            while ($ev = $evaluadores->fetch_assoc()):
                                                $sel = ($ev['id'] == $f['evaluador_id']) ? 'selected' : '';
                                        ?>
                                            <option value="<?php echo $ev['id']; ?>" <?php echo $sel; ?>>
                                                <?php echo htmlspecialchars($ev['name'].' '.$ev['lastName'].' ('.$ev['email'].')'); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                                <button type="submit" class="btn btn-sm btn-primary">Asignar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

<!-- ...existing code... -->


    <!--============================== footer Area End ==============================-->
    <footer class="footer-wrapper footer-layout2">
        <div class="widget-area">
            <div class="container">
                <div class="row justify-content-between">
                    <div class="col-md-6 col-xl-auto">
                        <div class="widget footer-widget">
                            <div class="th-widget-about">
                                <div class="about-logo text-center">
                                    <a href="https://ww2.ufps.edu.co" target="_blank"><img src="assets/img/img/ufps.png"
                                            alt="logo"></a>
                                </div>
                                <div class="th-social d-flex justify-content-center">
                                    <a href="https://www.facebook.com/Ufps.edu.co/" target="_blank"><i
                                            class="fab fa-facebook-f"></i></a>
                                    <a href="https://twitter.com/UFPSCUCUTA" target="_blank"><i
                                            class="fab fa-twitter"></i></a>
                                    <a href="https://www.instagram.com/ufpscucuta/" target="_blank"><i
                                            class="fab fa-instagram"></i></a>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6 col-xl-auto">
                        <div class="widget widget_nav_menu footer-widget">
                            <h3 class="widget_title">CONTACTO</h3>
                            <div class="menu-all-pages-container">
                                <p class="foot">Si tiene alguna duda puede enviarnos un correo electrónico a</p>
                                <p class="foot">SICTeI@ufps.edu.co<br>Correo electrónico del evento</p>
                            </div>
                        </div>

                    </div>


                    <div class="col-md-6 col-xl-auto">
                        <div class="widget footer-widget">
                            <h3 class="widget_title">LICENCIA CC</h3>
                            <div class="event-widget-wrap">
                                <div class="event-widget">
                                    <a rel="license" href="https://creativecommons.org/licenses/by-nc-nd/4.0/"
                                        target="_blank">
                                        <img alt="Licencia Creative Commons" style="border-width:0"
                                            src="https://i.creativecommons.org/l/by-nc-nd/4.0/88x31.png" />
                                    </a>
                                    <p class="foot"><br />
                                        Esta obra está bajo una</p>
                                    <p class="foot">Licencia Creative Commons </br>Atribución-NoComercial-SinDerivar </br>4.0
                                        Internacional.</p>


                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="copyright-wrap">
            <div class="container">
                <div class="row gy-2 align-items-center justify-content-center">
                    <div class="col-md-6 text-center">
                        <p class="copyright-text">Copyright <i class="fas fa-copyright"></i> 2025 . All Rights Reserved.
                        </p>
                    </div>
                </div>
            </div>
        </div>

    </footer>

    <!--******************************** Code End  Here  ******************************** -->
    <!-- Scroll To Top -->
    <div class="scroll-top">
        <svg class="progress-circle svg-content" width="100%" height="100%" viewBox="-1 -1 102 102">
            <path d="M50,1 a49,49 0 0,1 0,98 a49,49 0 0,1 0,-98"
                style="transition: stroke-dashoffset 10ms linear 0s; stroke-dasharray: 307.919, 307.919; stroke-dashoffset: 307.919;">
            </path>
        </svg>
    </div>

    <!--==============================
    All Js File
============================== -->
    <!-- Jquery -->
    <script src="assets/js/vendor/jquery-3.7.1.min.js"></script>
    <!-- Swiper Js -->
    <script src="assets/js/swiper-bundle.min.js"></script>
    <!-- Bootstrap -->
    <script src="assets/js/bootstrap.min.js"></script>
    <!-- Magnific Popup -->
    <script src="assets/js/jquery.magnific-popup.min.js"></script>
    <!-- Counter Up -->
    <script src="assets/js/jquery.counterup.min.js"></script>
    <!-- Tilt -->
    <script src="assets/js/tilt.jquery.min.js"></script>
    <!-- Isotope Filter -->
    <script src="assets/js/imagesloaded.pkgd.min.js"></script>
    <script src="assets/js/isotope.pkgd.min.js"></script>

    <!-- Main Js File -->
    <script src="assets/js/main.js"></script>
    <script>
        $(document).ready(function() {
            var swiper = new Swiper('.swiper-container', {
                spaceBetween: 30,
                centeredSlides: true,
                autoplay: {
                    delay: 5000,
                    disableOnInteraction: false,
                },
                pagination: {
                    el: '.swiper-pagination',
                    clickable: true,
                },
                navigation: {
                    nextEl: '.swiper-button-next',
                    prevEl: '.swiper-button-prev',
                },
            });
        });
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('select[name="eval_status"]').forEach(function(select) {

                function actualizarColor() {
                    select.classList.remove('pendiente', 'aprobado', 'rechazado');

                    if (select.value === 'Pendiente') {
                        select.classList.add('pendiente');
                    } else if (select.value === 'Aprobado') {
                        select.classList.add('aprobado');
                    } else if (select.value === 'Rechazado') {
                        select.classList.add('rechazado');
                    }
                }

                // Aplica color al cargar
                actualizarColor();

                // Cambia color cuando el usuario selecciona algo
                select.addEventListener('change', actualizarColor);
            });
        });
    </script>

    <script>
        // ID del video de YouTube que deseas reproducir
        var videoId = 'QEBWnECyTN4';

        // Cargar el reproductor cuando el documento esté listo
        $(document).ready(function() {
            var tag = document.createElement('script');
            tag.src = 'https://www.youtube.com/iframe_api';
            var firstScriptTag = document.getElementsByTagName('script')[0];
            firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

            var player;
            window.onYouTubeIframeAPIReady = function() {
                player = new YT.Player('player', {
                    height: '360',
                    width: '640',
                    videoId: videoId,
                    playerVars: {
                        'autoplay': 0, // 1 para autoplay
                        'controls': 1, // 0 para desactivar controles
                        'rel': 0, // 0 para desactivar videos relacionados al final
                        'showinfo': 0, // 0 para desactivar el título del video
                        'modestbranding': 1 // 1 para quitar el logo de YouTube
                    }
                });
            };
        });
    </script>
</body>

</html>