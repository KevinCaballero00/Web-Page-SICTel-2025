<?php
require_once 'config.php';
session_start();

// Permitir solo Evaluador o Administrador
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['Evaluador', 'Administrador'])) {
    header('HTTP/1.1 403 Forbidden');
    echo 'Acceso denegado.';
    exit;
}

// Actualizar estado de evaluación
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'update_status') {
    $id = intval($_POST['id'] ?? 0);
    $new = $_POST['eval_status'] ?? 'Pendiente';
    $stmt = $conn->prepare("UPDATE formularios SET eval_status = ?, revisado = 1 WHERE id = ?");
    $stmt->bind_param('si', $new, $id);
    $stmt->execute();
    $stmt->close();
    header('Location: gestion_formularios.php');
    exit;
}

// Mostrar detalle si se solicita
$detail = null;
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    $stmt = $conn->prepare("SELECT * FROM formularios WHERE id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $detail = $res->fetch_assoc();
    $stmt->close();
}

// Listado de ponencias
$result = $conn->query("SELECT id, name_form, institu, modalidad, programa, estado, eval_status, revisado FROM formularios ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion de Ponencias por Evaluador</title>
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
    <link rel="stylesheet" href="assets/css/styleGestion.css">

</head>

<body>
    <!--============================== Mobile Menu ============================== -->
    <div class="th-menu-wrapper">
        <div class="th-menu-area text-center">
            <button class="th-menu-toggle"><i class="fal fa-times"></i></button>
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

    <div class="body-form">
        <div class="container">
            <div class="form-box">
                <h2 class="sec-title">GESTIÓN DE PONENCIAS</h2>

                <p class="evaluador">Evaluador: <?php echo htmlspecialchars($_SESSION['name'] ?? $_SESSION['email'] ?? ''); ?></p>

                <table class="table-gestion" aria-describedby="Listado de ponencias">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Título de la Ponencia</th>
                            <th>Institución a la que Pertenece</th>
                            <th>Modalidad de Participación</th>
                            <th>Área de Conocimiento</th>
                            <th>Estado del Proyecto</th>
                            <th>Revisado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $row['id']; ?></td>
                                <td><?php echo htmlspecialchars($row['name_form']); ?></td>
                                <td><?php echo htmlspecialchars($row['institu']); ?></td>
                                <td><?php echo htmlspecialchars($row['modalidad']); ?></td>
                                <td><?php echo htmlspecialchars($row['programa']); ?></td>
                                <td class="status-<?php echo htmlspecialchars($row['eval_status']); ?>"><?php echo htmlspecialchars($row['eval_status']); ?></td>
                                <td><?php echo $row['revisado'] ? 'Sí' : 'No'; ?></td>
                                <td class="row-actions">
                                    <form method="post" action="gestion_formularios.php" class="form-acciones">
                                        <a class="btn-inline ver" href="gestion_formularios.php?id=<?php echo $row['id']; ?>">Ver</a>
                                        <input type="hidden" name="action" value="update_status">
                                        <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                        <select name="eval_status">
                                            <option value="Pendiente" <?php if ($row['eval_status'] == 'Pendiente') echo 'selected'; ?>>Pendiente</option>
                                            <option value="Aprobado" <?php if ($row['eval_status'] == 'Aprobado') echo 'selected'; ?>>Aprobado</option>
                                            <option value="Rechazado" <?php if ($row['eval_status'] == 'Rechazado') echo 'selected'; ?>>Rechazado</option>
                                        </select>
                                        <button type="submit" class="btn-inline guardar">Guardar</button>
                                    </form>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>

                <?php if ($detail): ?>
                    <hr>
                    <h2 class="sec-title">Detalle Ponencia #<?php echo $detail['id']; ?></h2>
                    <p><strong>Título:</strong> <?php echo htmlspecialchars($detail['name_form']); ?></p>
                    <p><strong>Modalidad:</strong> <?php echo htmlspecialchars($detail['modalidad']); ?></p>
                    <p><strong>Estado del proyecto:</strong> <?php echo htmlspecialchars($detail['estado']); ?></p>
                    <p><strong>Documento:</strong> <?php echo htmlspecialchars($detail['documento']); ?> - <?php echo htmlspecialchars($detail['num_doc']); ?></p>
                    <p><strong>Teléfono:</strong> <?php echo htmlspecialchars($detail['telefono']); ?></p>
                    <p><strong>Nivel:</strong> <?php echo htmlspecialchars($detail['niv_estu']); ?></p>
                    <p><strong>Programa:</strong> <?php echo htmlspecialchars($detail['programa']); ?></p>
                    <p><strong>Grupo:</strong> <?php echo htmlspecialchars($detail['grupo_inv']); ?></p>
                    <p><strong>Institución:</strong> <?php echo htmlspecialchars($detail['institu']); ?></p>
                    <p><strong>Ubicación:</strong> <?php echo htmlspecialchars($detail['ubicacion']); ?></p>
                    <?php if (!empty($detail['archivo_pdf'])): ?>
                        <p><strong>Ponencia en formato WORD:</strong>
                            <a href="<?php echo htmlspecialchars($detail['archivo_pdf']); ?>" target="_blank">Descargar Ponencia</a>
                        </p>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

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