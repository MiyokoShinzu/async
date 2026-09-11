
<?php

/* =========================================================
   ETS-ASYNC LEARNING PORTAL
   SUPPORT LEADERBOARDS
========================================================== */

session_start();


/* =========================================================
   AUTHENTICATION
========================================================== */

if (
    !isset($_SESSION["logged_in"]) ||
    $_SESSION["logged_in"] !== true ||
    !isset($_SESSION["user"]) ||
    ($_SESSION["user"]["access"] ?? "") !== "student"
) {

    header("Location: ../login.php");
    exit;
}


/* =========================================================
   USER DATA
========================================================== */

$user = $_SESSION["user"];

$currentStudentId =
    $user["student_id"] ?? "";


/* =========================================================
   DATABASE CONNECTION
========================================================== */

require_once "../src/connection.php";


/* =========================================================
   LEADERBOARD DATA
========================================================== */

/*
   IMPORTANT:

   Only successful / paid transactions are included.

   If your database uses another status such as
   "completed", change 'paid' below.
*/


/* =========================================================
   TOP SUPPORTERS
========================================================== */

$leaderboard = [];


/*
   We group by donor name.

   This allows multiple successful support transactions
   from the same person to be combined.
*/

$sql = "
    SELECT
        donor_name,
        SUM(amount) AS total_support,
        COUNT(*) AS support_count
    FROM support_transactions
    WHERE status = 'paid'
    GROUP BY donor_name
    ORDER BY total_support DESC, donor_name ASC
    LIMIT 100
";


$result = $mysqli->query($sql);


if ($result) {

    while ($row = $result->fetch_assoc()) {

        $leaderboard[] = $row;
    }

    $result->free();
}


/* =========================================================
   TOTAL SUPPORT
========================================================== */

$totalSupport = 0;

$stmt = $mysqli->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM support_transactions
    WHERE status = 'paid'
");

if ($stmt) {

    $stmt->execute();

    $stmt->bind_result($totalSupport);

    $stmt->fetch();

    $stmt->close();
}


/* =========================================================
   TOTAL SUPPORTERS
========================================================== */

$totalSupporters = 0;

$stmt = $mysqli->prepare("
    SELECT COUNT(DISTINCT donor_name)
    FROM support_transactions
    WHERE status = 'paid'
");

if ($stmt) {

    $stmt->execute();

    $stmt->bind_result($totalSupporters);

    $stmt->fetch();

    $stmt->close();
}


/* =========================================================
   TOTAL TRANSACTIONS
========================================================== */

$totalTransactions = 0;

$stmt = $mysqli->prepare("
    SELECT COUNT(*)
    FROM support_transactions
    WHERE status = 'paid'
");

if ($stmt) {

    $stmt->execute();

    $stmt->bind_result($totalTransactions);

    $stmt->fetch();

    $stmt->close();
}


/* =========================================================
   CURRENT USER TOTAL SUPPORT
========================================================== */

$currentUserSupport = 0;


/*
   We use the student_id to identify the logged-in student.

   This is more reliable than matching the displayed name.
*/

$stmt = $mysqli->prepare("
    SELECT COALESCE(SUM(amount), 0)
    FROM support_transactions
    WHERE status = 'paid'
      AND student_id = ?
");

if ($stmt) {

    $stmt->bind_param(
        "s",
        $currentStudentId
    );

    $stmt->execute();

    $stmt->bind_result($currentUserSupport);

    $stmt->fetch();

    $stmt->close();
}


/* =========================================================
   CURRENT USER RANK
========================================================== */

$currentUserRank = null;


/*
   Determine the student's rank based on total support.
*/

if ($currentUserSupport > 0) {

    $stmt = $mysqli->prepare("
        SELECT COUNT(*) + 1
        FROM (
            SELECT
                student_id,
                SUM(amount) AS total_support
            FROM support_transactions
            WHERE status = 'paid'
              AND student_id IS NOT NULL
            GROUP BY student_id
            HAVING total_support > ?
        ) AS rankings
    ");

    if ($stmt) {

        $stmt->bind_param(
            "d",
            $currentUserSupport
        );

        $stmt->execute();

        $stmt->bind_result($currentUserRank);

        $stmt->fetch();

        $stmt->close();
    }
}


/* =========================================================
   HELPER FUNCTION
   MASK / FORMAT SUPPORTER NAME
========================================================== */

/*
   Example:

   Karl Stephen Evallo
   becomes:

   Karl S. Evallo

   This gives the leaderboard a cleaner presentation
   while avoiding unnecessary exposure of full names.
*/

function formatSupporterName($name)
{

    $name = trim($name);

    if ($name === "") {
        return "Anonymous Supporter";
    }

    $parts = preg_split(
        '/\s+/',
        $name
    );

    if (count($parts) <= 2) {
        return htmlspecialchars(
            $name,
            ENT_QUOTES,
            "UTF-8"
        );
    }

    $firstName = $parts[0];

    $lastName = end($parts);

    $middleInitials = "";

    for (
        $i = 1;
        $i < count($parts) - 1;
        $i++
    ) {

        if ($parts[$i] !== "") {

            $middleInitials .=
                strtoupper(
                    substr(
                        $parts[$i],
                        0,
                        1
                    )
                ) . ". ";
        }
    }

    return htmlspecialchars(
        $firstName .
            " " .
            $middleInitials .
            $lastName,
        ENT_QUOTES,
        "UTF-8"
    );
}


/* =========================================================
   PAGE VARIABLES
========================================================== */

$firstName = htmlspecialchars(
    $user["first_name"] ?? "",
    ENT_QUOTES,
    "UTF-8"
);

$lastName = htmlspecialchars(
    $user["last_name"] ?? "",
    ENT_QUOTES,
    "UTF-8"
);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <?php require_once "globals/head.php"; ?>

    <title>Leaderboards | ETS-Async</title>


    <style>
        /* =====================================================
           LEADERBOARD VARIABLES
        ====================================================== */

        :root {

            --leader-blue: #2563eb;
            --leader-blue-dark: #1d4ed8;

            --leader-bg: #f8fafc;
            --leader-card: #ffffff;

            --leader-text: #0f172a;
            --leader-muted: #64748b;

            --leader-border: #e2e8f0;

            --leader-success: #16a34a;

        }


        /* =====================================================
           DARK MODE
        ====================================================== */

        html[data-theme="dark"] {

            --leader-bg: #0f172a;
            --leader-card: #1e293b;

            --leader-text: #f8fafc;
            --leader-muted: #94a3b8;

            --leader-border: #334155;

        }


        /* =====================================================
           MAIN CONTAINER
        ====================================================== */

        .leaderboard-layout {

            max-width: 1100px;

            margin: 0 auto;

        }


        /* =====================================================
           HEADER
        ====================================================== */

        .leaderboard-header {

            margin-bottom: 30px;

        }

        .leaderboard-header h1 {

            color: var(--leader-text);

            font-weight: 700;

        }


        .leaderboard-header p {

            color: var(--leader-muted);

        }


        /* =====================================================
           STAT CARDS
        ====================================================== */

        .stat-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 15px;

            margin-bottom: 25px;

        }


        .stat-card {

            background: var(--leader-card);

            border: 1px solid var(--leader-border);

            border-radius: 15px;

            padding: 22px;

            box-shadow:
                0 6px 20px rgba(0, 0, 0, .04);

        }


        .stat-icon {

            width: 45px;
            height: 45px;

            border-radius: 12px;

            display: flex;

            align-items: center;

            justify-content: center;

            background:
                rgba(37, 99, 235, .1);

            color: var(--leader-blue);

            font-size: 20px;

            margin-bottom: 12px;

        }


        .stat-label {

            color: var(--leader-muted);

            font-size: 13px;

            margin-bottom: 4px;

        }


        .stat-value {

            color: var(--leader-text);

            font-size: 24px;

            font-weight: 700;

        }


        /* =====================================================
           USER RANK CARD
        ====================================================== */

        .my-rank-card {

            background:
                linear-gradient(135deg,
                    var(--leader-blue),
                    var(--leader-blue-dark));

            color: white;

            border-radius: 16px;

            padding: 22px;

            margin-bottom: 25px;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

        }


        .my-rank-title {

            font-size: 13px;

            opacity: .85;

            margin-bottom: 4px;

        }


        .my-rank-name {

            font-size: 20px;

            font-weight: 700;

        }


        .my-rank-number {

            font-size: 34px;

            font-weight: 800;

            white-space: nowrap;

        }


        /* =====================================================
           LEADERBOARD CARD
        ====================================================== */

        .leaderboard-card {

            background: var(--leader-card);

            border: 1px solid var(--leader-border);

            border-radius: 18px;

            overflow: hidden;

            box-shadow:
                0 10px 30px rgba(0, 0, 0, .05);

        }


        /* =====================================================
           LEADERBOARD TITLE
        ====================================================== */

        .leaderboard-card-header {

            padding: 24px;

            border-bottom:
                1px solid var(--leader-border);

        }


        .leaderboard-card-header h2 {

            color: var(--leader-text);

            font-size: 20px;

            font-weight: 700;

            margin: 0;

        }


        .leaderboard-card-header p {

            color: var(--leader-muted);

            font-size: 14px;

            margin: 5px 0 0;

        }


        /* =====================================================
           LEADERBOARD ROW
        ====================================================== */

        .leader-row {

            display: grid;

            grid-template-columns:
                70px 1fr 120px 120px;

            align-items: center;

            gap: 15px;

            padding: 18px 24px;

            border-bottom:
                1px solid var(--leader-border);

            transition: .2s;

        }


        .leader-row:last-child {

            border-bottom: none;

        }


        .leader-row:hover {

            background:
                rgba(37, 99, 235, .04);

        }


        /* =====================================================
           CURRENT USER
        ====================================================== */

        .leader-row.current-user {

            background:
                rgba(37, 99, 235, .08);

        }


        /* =====================================================
           RANK
        ====================================================== */

        .rank {

            font-size: 20px;

            font-weight: 800;

            color: var(--leader-muted);

            text-align: center;

        }


        .rank.top-rank {

            font-size: 25px;

        }


        /* =====================================================
           SUPPORTER
        ====================================================== */

        .supporter {

            display: flex;

            align-items: center;

            gap: 12px;

            min-width: 0;

        }


        .supporter-avatar {

            width: 42px;
            height: 42px;

            border-radius: 50%;

            background:
                rgba(37, 99, 235, .1);

            color: var(--leader-blue);

            display: flex;

            align-items: center;

            justify-content: center;

            font-weight: 700;

            flex-shrink: 0;

        }


        .supporter-name {

            color: var(--leader-text);

            font-weight: 600;

            overflow: hidden;

            text-overflow: ellipsis;

            white-space: nowrap;

        }


        .you-badge {

            display: inline-block;

            margin-left: 7px;

            padding: 3px 7px;

            border-radius: 20px;

            background: var(--leader-blue);

            color: white;

            font-size: 10px;

            font-weight: 700;

            vertical-align: middle;

        }


        /* =====================================================
           SUPPORT COUNT
        ====================================================== */

        .support-count {

            color: var(--leader-muted);

            text-align: center;

            font-size: 14px;

        }


        /* =====================================================
           SUPPORT AMOUNT
        ====================================================== */

        .support-amount {

            color: var(--leader-success);

            font-weight: 700;

            text-align: right;

        }


        /* =====================================================
           EMPTY STATE
        ====================================================== */

        .empty-state {

            text-align: center;

            padding: 60px 25px;

        }


        .empty-icon {

            width: 70px;
            height: 70px;

            border-radius: 50%;

            background:
                rgba(37, 99, 235, .1);

            color: var(--leader-blue);

            display: flex;

            align-items: center;

            justify-content: center;

            margin: 0 auto 20px;

            font-size: 30px;

        }


        .empty-state h3 {

            color: var(--leader-text);

            font-weight: 700;

        }


        .empty-state p {

            color: var(--leader-muted);

        }


        /* =====================================================
           SUPPORT BUTTON
        ====================================================== */

        .support-button {

            display: inline-block;

            background: var(--leader-blue);

            color: white;

            text-decoration: none;

            padding: 12px 20px;

            border-radius: 9px;

            font-weight: 600;

            transition: .2s;

        }


        .support-button:hover {

            background:
                var(--leader-blue-dark);

            color: white;

        }


        /* =====================================================
           RESPONSIVE
        ====================================================== */

        @media(max-width: 800px) {

            .stat-grid {

                grid-template-columns:
                    1fr;

            }


            .leader-row {

                grid-template-columns:
                    55px 1fr 100px;

            }


            .support-count {

                display: none;

            }

        }


        @media(max-width: 550px) {

            .my-rank-card {

                flex-direction: column;

                align-items: flex-start;

            }


            .my-rank-number {

                font-size: 28px;

            }


            .leader-row {

                grid-template-columns:
                    45px 1fr 90px;

                padding:
                    15px;

            }


            .support-amount {

                font-size: 13px;

            }


            .supporter-avatar {

                width: 36px;
                height: 36px;

            }

        }
    </style>

</head>


<body>

    <?php require_once "globals/sidebar.php"; ?>

    <?php require_once "globals/topbar.php"; ?>


    <main class="main-content">

        <div class="content-wrapper">


            <!-- =================================================
                 PAGE HEADER
            ================================================== -->

            <div class="leaderboard-layout">

                <div class="leaderboard-header">

                    <h1 class="fw-bold">

                        <i class="bi bi-trophy me-2"></i>

                        Support Leaderboard

                    </h1>

                    <p>

                        See the supporters who help keep
                        ETS-Async learning resources growing.

                    </p>

                </div>


                <!-- =================================================
                     STATISTICS
                ================================================== -->

                <div class="stat-grid">


                    <!-- TOTAL SUPPORT -->

                    <div class="stat-card">

                        <div class="stat-icon">

                            <i class="bi bi-heart-fill"></i>

                        </div>

                        <div class="stat-label">

                            Total Support

                        </div>

                        <div class="stat-value">

                            ₱<?= number_format(
                                    (float)$totalSupport,
                                    2
                                ) ?>

                        </div>

                    </div>


                    <!-- SUPPORTERS -->

                    <div class="stat-card">

                        <div class="stat-icon">

                            <i class="bi bi-people-fill"></i>

                        </div>

                        <div class="stat-label">

                            Supporters

                        </div>

                        <div class="stat-value">

                            <?= number_format(
                                (int)$totalSupporters
                            ) ?>

                        </div>

                    </div>


                    <!-- TRANSACTIONS -->

                    <div class="stat-card">

                        <div class="stat-icon">

                            <i class="bi bi-receipt"></i>

                        </div>

                        <div class="stat-label">

                            Successful Supports

                        </div>

                        <div class="stat-value">

                            <?= number_format(
                                (int)$totalTransactions
                            ) ?>

                        </div>

                    </div>

                </div>


                <!-- =================================================
                     MY RANK
                ================================================== -->

                <?php if ($currentUserSupport > 0): ?>

                    <div class="my-rank-card">

                        <div>

                            <div class="my-rank-title">

                                YOUR SUPPORT

                            </div>

                            <div class="my-rank-name">

                                <?= $firstName ?>
                                <?= $lastName ?>

                            </div>

                            <div class="mt-1">

                                Total contribution:
                                <strong>
                                    ₱<?= number_format(
                                            (float)$currentUserSupport,
                                            2
                                        ) ?>
                                </strong>

                            </div>

                        </div>


                        <div class="text-end">

                            <div class="my-rank-title">

                                CURRENT RANK

                            </div>

                            <div class="my-rank-number">

                                #<?= (int)$currentUserRank ?>

                            </div>

                        </div>

                    </div>

                <?php endif; ?>


                <!-- =================================================
                     LEADERBOARD
                ================================================== -->

                <div class="leaderboard-card">


                    <div class="leaderboard-card-header">

                        <h2>

                            <i class="bi bi-trophy me-2"></i>

                            Top Supporters

                        </h2>

                        <p>

                            Rankings are based on total
                            successful support contributions.

                        </p>

                    </div>


                    <?php if (count($leaderboard) > 0): ?>


                        <!-- =========================================
                             TABLE HEADER
                        ========================================== -->

                        <div
                            class="leader-row d-none d-md-grid"
                            style="font-size: 12px; font-weight: 700; color: var(--leader-muted);">

                            <div class="text-center">

                                RANK

                            </div>

                            <div>

                                SUPPORTER

                            </div>

                            <div class="text-center">

                                SUPPORTS

                            </div>

                            <div class="text-end">

                                TOTAL

                            </div>

                        </div>


                        <!-- =========================================
                             LEADERBOARD ITEMS
                        ========================================== -->

                        <?php

                        $rank = 1;

                        foreach (
                            $leaderboard
                            as $supporter
                        ):

                            /*
                               Determine whether this row
                               belongs to the current student.

                               We compare names here because the
                               leaderboard is grouped by donor_name.
                            */

                            $supporterName =
                                trim(
                                    $supporter["donor_name"]
                                        ?? ""
                                );

                            $currentFullName =
                                trim(
                                    ($user["first_name"] ?? "") .
                                        " " .
                                        ($user["last_name"] ?? "")
                                );

                            $isCurrentUser =
                                strcasecmp(
                                    $supporterName,
                                    $currentFullName
                                ) === 0;


                            /* =====================================
                               RANK ICON
                            ====================================== */

                            if ($rank === 1) {

                                $rankDisplay =
                                    '<i class="bi bi-trophy-fill"></i>';
                            } elseif ($rank === 2) {

                                $rankDisplay =
                                    '<i class="bi bi-award-fill"></i>';
                            } elseif ($rank === 3) {

                                $rankDisplay =
                                    '<i class="bi bi-award-fill"></i>';
                            } else {

                                $rankDisplay =
                                    "#" . $rank;
                            }


                            /* =====================================
                               AVATAR LETTER
                            ====================================== */

                            $avatarLetter =
                                strtoupper(
                                    substr(
                                        $supporterName !== ""
                                            ? $supporterName
                                            : "A",
                                        0,
                                        1
                                    )
                                );

                        ?>

                            <div
                                class="
                                    leader-row
                                    <?= $isCurrentUser
                                        ? 'current-user'
                                        : '' ?>
                                ">


                                <!-- RANK -->

                                <div
                                    class="
                                        rank
                                        <?= $rank <= 3
                                            ? 'top-rank'
                                            : '' ?>
                                    ">

                                    <?= $rankDisplay ?>

                                </div>


                                <!-- SUPPORTER -->

                                <div class="supporter">

                                    <div class="supporter-avatar">

                                        <?= htmlspecialchars(
                                            $avatarLetter,
                                            ENT_QUOTES,
                                            "UTF-8"
                                        ) ?>

                                    </div>


                                    <div
                                        class="supporter-name"
                                        title="<?= htmlspecialchars(
                                                    $supporterName,
                                                    ENT_QUOTES,
                                                    "UTF-8"
                                                ) ?>">

                                        <?= formatSupporterName(
                                            $supporterName
                                        ) ?>


                                        <?php if (
                                            $isCurrentUser
                                        ): ?>

                                            <span class="you-badge">

                                                YOU

                                            </span>

                                        <?php endif; ?>

                                    </div>

                                </div>


                                <!-- SUPPORT COUNT -->

                                <div class="support-count">

                                    <?= number_format(
                                        (int)(
                                            $supporter["support_count"] ?? 0
                                        )
                                    ) ?>

                                    <span class="d-none d-md-inline">

                                        supports

                                    </span>

                                </div>


                                <!-- AMOUNT -->

                                <div class="support-amount">

                                    ₱<?= number_format(
                                            (float)(
                                                $supporter["total_support"] ?? 0
                                            ),
                                            2
                                        ) ?>

                                </div>


                            </div>


                        <?php

                            $rank++;

                        endforeach;

                        ?>


                    <?php else: ?>


                        <!-- =========================================
                             EMPTY STATE
                        ========================================== -->

                        <div class="empty-state">

                            <div class="empty-icon">

                                <i class="bi bi-trophy"></i>

                            </div>

                            <h3>

                                No supporters yet

                            </h3>

                            <p>

                                Be the first to support the
                                continued development of ETS-Async.

                            </p>


                            <a
                                href="support_creator.php"
                                class="support-button">

                                <i class="bi bi-heart me-2"></i>

                                Support ETS-Async

                            </a>

                        </div>


                    <?php endif; ?>


                </div>


                <!-- =================================================
                     FOOTER INFORMATION
                ================================================== -->

                <div
                    class="text-center mt-4"
                    style="
                        color: var(--leader-muted);
                        font-size: 13px;
                    ">

                    <i class="bi bi-info-circle me-1"></i>

                    Thank you to everyone who supports
                    ETS-Async and its educational resources.

                </div>


            </div>

        </div>

    </main>


    <?php require_once "globals/scripts.php"; ?>


</body>

</html>
