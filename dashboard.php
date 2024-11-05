<?php
session_start();
require "amfcConx.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: logPage.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch earnings data
try {
    // Fetch daily earnings for the last 7 days
    $stmtDaily = $pdo->prepare("SELECT DATE(ORDER_DATETIME) as date, SUM(ORDER_TOTALAMOUNT) as total FROM ORDERPROD WHERE ORDER_STATUS = 1 AND ORDER_DATETIME >= DATE_SUB(CURDATE(), INTERVAL 7 DAY) GROUP BY DATE(ORDER_DATETIME)");
    $stmtDaily->execute();
    $dailyEarnings = $stmtDaily->fetchAll(PDO::FETCH_ASSOC);

    // Fetch weekly earnings for the last 4 weeks
    $stmtWeekly = $pdo->prepare("SELECT YEARWEEK(ORDER_DATETIME, 1) as week, SUM(ORDER_TOTALAMOUNT) as total FROM ORDERPROD WHERE ORDER_STATUS = 1 AND ORDER_DATETIME >= DATE_SUB(CURDATE(), INTERVAL 4 WEEK) GROUP BY YEARWEEK(ORDER_DATETIME, 1)");
    $stmtWeekly->execute();
    $weeklyEarnings = $stmtWeekly->fetchAll(PDO::FETCH_ASSOC);

    // Fetch monthly earnings for the last 12 months
    $stmtMonthly = $pdo->prepare("SELECT DATE_FORMAT(ORDER_DATETIME, '%Y-%m') as month, SUM(ORDER_TOTALAMOUNT) as total FROM ORDERPROD WHERE ORDER_STATUS = 1 AND ORDER_DATETIME >= DATE_SUB(CURDATE(), INTERVAL 12 MONTH) GROUP BY DATE_FORMAT(ORDER_DATETIME, '%Y-%m')");
    $stmtMonthly->execute();
    $monthlyEarnings = $stmtMonthly->fetchAll(PDO::FETCH_ASSOC);

    // Fetch yearly earnings for the last 5 years
    $stmtYearly = $pdo->prepare("SELECT DATE_FORMAT(ORDER_DATETIME, '%Y') as year, SUM(ORDER_TOTALAMOUNT) as total FROM ORDERPROD WHERE ORDER_STATUS = 1 AND ORDER_DATETIME >= DATE_SUB(CURDATE(), INTERVAL 5 YEAR) GROUP BY DATE_FORMAT(ORDER_DATETIME, '%Y')");
    $stmtYearly->execute();
    $yearlyEarnings = $stmtYearly->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        .chart-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .chart-container {
            width: 95%;
            max-width: 1000px;
            height: 400px; /* Increased height */
            background-color: #fff;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 10px;
        }

        .filter-container {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
        }

        .filter-container label, .filter-container select {
            margin: 0 5px;
        }

        a.back-to-admin-page {
            text-decoration: none;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            background-color: #007bff;
            transition: background-color 0.3s;
        }

        a.back-to-admin-page:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <h1>Admin Dashboard</h1>
    <div style="text-align: center; margin-top: 20px;">
        <a href="adminPage.php" class="back-to-admin-page">Back to Admin Page</a>
    </div>
    <div class="chart-grid">
        <div class="chart-container">
            <h2>Yearly Earnings (Last 5 Years)</h2>
            <div class="filter-container">
                <label for="yearFilter">Year:</label>
                <select id="yearFilter">
                    <option value="">All</option>
                    <!-- Add options dynamically -->
                </select>
            </div>
            <canvas id="yearlyEarningsChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Daily Earnings (Last 7 Days)</h2>
            <div class="filter-container">
                <label for="dailyFilter">Date:</label>
                <select id="dailyFilter">
                    <option value="">All</option>
                    <!-- Add options dynamically -->
                </select>
            </div>
            <canvas id="dailyEarningsChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Weekly Earnings (Last 4 Weeks)</h2>
            <div class="filter-container">
                <label for="weeklyFilter">Week:</label>
                <select id="weeklyFilter">
                    <option value="">All</option>
                    <!-- Add options dynamically -->
                </select>
            </div>
            <canvas id="weeklyEarningsChart"></canvas>
        </div>
        <div class="chart-container">
            <h2>Monthly Earnings (Last 12 Months)</h2>
            <div class="filter-container">
                <label for="monthlyFilter">Month:</label>
                <select id="monthlyFilter">
                    <option value="">All</option>
                    <!-- Add options dynamically -->
                </select>
            </div>
            <canvas id="monthlyEarningsChart"></canvas>
        </div>
    </div>

    <script>
        // Earnings Data
        const yearlyEarningsLabels = <?= json_encode(array_column($yearlyEarnings, 'year')) ?>;
        const yearlyEarningsData = <?= json_encode(array_column($yearlyEarnings, 'total')) ?>;
        const dailyEarningsLabels = <?= json_encode(array_column($dailyEarnings, 'date')) ?>;
        const dailyEarningsData = <?= json_encode(array_column($dailyEarnings, 'total')) ?>;
        const weeklyEarningsLabels = <?= json_encode(array_column($weeklyEarnings, 'week')) ?>;
        const weeklyEarningsData = <?= json_encode(array_column($weeklyEarnings, 'total')) ?>;
        const monthlyEarningsLabels = <?= json_encode(array_column($monthlyEarnings, 'month')) ?>;
        const monthlyEarningsData = <?= json_encode(array_column($monthlyEarnings, 'total')) ?>;

        // Populate Filter Options
        function populateFilterOptions(labels, filterId) {
            const filter = document.getElementById(filterId);
            labels.forEach(label => {
                const option = document.createElement('option');
                option.value = label;
                option.textContent = label;
                filter.appendChild(option);
            });
        }

        populateFilterOptions(yearlyEarningsLabels, 'yearFilter');
        populateFilterOptions(dailyEarningsLabels, 'dailyFilter');
        populateFilterOptions(weeklyEarningsLabels, 'weeklyFilter');
        populateFilterOptions(monthlyEarningsLabels, 'monthlyFilter');

        // Update Chart Data Based on Filter
        function updateChartData(chart, labels, data, filterValue) {
            const filteredLabels = labels.filter((label, index) => filterValue === '' || label === filterValue);
            const filteredData = data.filter((_, index) => filterValue === '' || labels[index] === filterValue);
            chart.data.labels = filteredLabels;
            chart.data.datasets[0].data = filteredData;
            chart.update();
        }

        // Chart Configurations
        const yearlyEarningsConfig = {
            type: 'bar',
            data: {
                labels: yearlyEarningsLabels,
                datasets: [{
                    label: 'Yearly Earnings',
                    data: yearlyEarningsData,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Year' } },
                    y: { title: { display: true, text: 'Earnings ($)' } }
                },
                layout: {
                    padding: {
                        bottom: 20
                    }
                }
            }
        };

        const dailyEarningsConfig = {
            type: 'line',
            data: {
                labels: dailyEarningsLabels,
                datasets: [{
                    label: 'Daily Earnings',
                    data: dailyEarningsData,
                    borderColor: 'rgba(75, 192, 192, 1)',
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Date' } },
                    y: { title: { display: true, text: 'Earnings ($)' } }
                },
                layout: {
                    padding: {
                        bottom: 20
                    }
                }
            }
        };

        const weeklyEarningsConfig = {
            type: 'line',
            data: {
                labels: weeklyEarningsLabels,
                datasets: [{
                    label: 'Weekly Earnings',
                    data: weeklyEarningsData,
                    borderColor: 'rgba(153, 102, 255, 1)',
                    backgroundColor: 'rgba(153, 102, 255, 0.2)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Week' } },
                    y: { title: { display: true, text: 'Earnings ($)' } }
                },
                layout: {
                    padding: {
                        bottom: 20
                    }
                }
            }
        };

        const monthlyEarningsConfig = {
            type: 'line',
            data: {
                labels: monthlyEarningsLabels,
                datasets: [{
                    label: 'Monthly Earnings',
                    data: monthlyEarningsData,
                    borderColor: 'rgba(255, 159, 64, 1)',
                    backgroundColor: 'rgba(255, 159, 64, 0.2)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    x: { title: { display: true, text: 'Month' } },
                    y: { title: { display: true, text: 'Earnings ($)' } }
                },
                layout: {
                    padding: {
                        bottom: 20
                    }
                }
            }
        };

        // Render Charts
        const yearlyEarningsChart = new Chart(document.getElementById('yearlyEarningsChart'), yearlyEarningsConfig);
        const dailyEarningsChart = new Chart(document.getElementById('dailyEarningsChart'), dailyEarningsConfig);
        const weeklyEarningsChart = new Chart(document.getElementById('weeklyEarningsChart'), weeklyEarningsConfig);
        const monthlyEarningsChart = new Chart(document.getElementById('monthlyEarningsChart'), monthlyEarningsConfig);

        // Add Event Listeners for Filters
        document.getElementById('yearFilter').addEventListener('change', (e) => {
            updateChartData(yearlyEarningsChart, yearlyEarningsLabels, yearlyEarningsData, e.target.value);
        });

        document.getElementById('dailyFilter').addEventListener('change', (e) => {
            updateChartData(dailyEarningsChart, dailyEarningsLabels, dailyEarningsData, e.target.value);
        });

        document.getElementById('weeklyFilter').addEventListener('change', (e) => {
            updateChartData(weeklyEarningsChart, weeklyEarningsLabels, weeklyEarningsData, e.target.value);
        });

        document.getElementById('monthlyFilter').addEventListener('change', (e) => {
            updateChartData(monthlyEarningsChart, monthlyEarningsLabels, monthlyEarningsData, e.target.value);
        });
    </script>
</body>
</html>
