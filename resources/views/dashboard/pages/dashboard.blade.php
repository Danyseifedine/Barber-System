@extends('dashboard.layout.index')

@section('content')
    <div class="row g-5 g-xl-10 mb-5 mb-xl-10">
        <!-- Statistics Cards -->
        <div class="col-xl-3 col-md-6 mb-5">
            <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                <div class="card-header">
                    <h3 class="card-title">Total Appointments</h3>
                </div>
                <div class="card-body d-flex align-items-center pt-0">
                    <div class="d-flex flex-column flex-grow-1">
                        <span class="fw-bolder text-gray-800 fs-1">{{ $chartData['statistics']['totalAppointments'] }}</span>
                        <span class="text-gray-400 pt-1 fw-semibold fs-6">All time</span>
                    </div>
                    <span class="symbol symbol-60px">
                        <span class="symbol-label bg-light-primary">
                            <i class="bi bi-calendar-check fs-1 text-primary"></i>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-5">
            <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                <div class="card-header">
                    <h3 class="card-title">Total Revenue</h3>
                </div>
                <div class="card-body d-flex align-items-center pt-0">
                    <div class="d-flex flex-column flex-grow-1">
                        <span
                            class="fw-bolder text-gray-800 fs-1">${{ number_format($chartData['statistics']['totalRevenue'], 2) }}</span>
                        <span class="text-gray-400 pt-1 fw-semibold fs-6">All time</span>
                    </div>
                    <span class="symbol symbol-60px">
                        <span class="symbol-label bg-light-success">
                            <i class="bi bi-cash-stack fs-1 text-success"></i>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-5">
            <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                <div class="card-header">
                    <h3 class="card-title">Total Customers</h3>
                </div>
                <div class="card-body d-flex align-items-center pt-0">
                    <div class="d-flex flex-column flex-grow-1">
                        <span class="fw-bolder text-gray-800 fs-1">{{ $chartData['statistics']['totalCustomers'] }}</span>
                        <span class="text-gray-400 pt-1 fw-semibold fs-6">All time</span>
                    </div>
                    <span class="symbol symbol-60px">
                        <span class="symbol-label bg-light-info">
                            <i class="bi bi-people fs-1 text-info"></i>
                        </span>
                    </span>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-5">
            <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                <div class="card-header">
                    <h3 class="card-title">Popular Service</h3>
                </div>
                <div class="card-body d-flex align-items-center pt-0">
                    <div class="d-flex flex-column flex-grow-1">
                        <span
                            class="fw-bolder text-gray-800 fs-1">{{ $chartData['statistics']['popularService']['name'] }}</span>
                        <span
                            class="text-gray-400 pt-1 fw-semibold fs-6">{{ $chartData['statistics']['popularService']['count'] }}
                            bookings</span>
                    </div>
                    <span class="symbol symbol-60px">
                        <span class="symbol-label bg-light-warning">
                            <i class="bi bi-scissors fs-1 text-warning"></i>
                        </span>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-5 g-xl-10">
        <!-- Chart 1: Monthly Appointments -->
        <div class="col-xl-6 mb-5 mb-xl-10">
            <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Monthly Appointments</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Appointment trends over time</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="appointmentsChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 2: Revenue Trends -->
        <div class="col-xl-6 mb-5 mb-xl-10">
            <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Revenue Trends</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Monthly revenue performance</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="revenueChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 3: Service Popularity -->
        <div class="col-xl-6 mb-5 mb-xl-10">
            <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Service Popularity</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Most booked services</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="servicesChart"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Chart 4: Appointment Status Distribution -->
        <div class="col-xl-6 mb-5 mb-xl-10">
            <div class="card card-flush h-100 shadow-sm hover-elevate-up">
                <div class="card-header border-0 pt-5">
                    <h3 class="card-title align-items-start flex-column">
                        <span class="card-label fw-bold fs-3 mb-1">Appointment Status</span>
                        <span class="text-muted mt-1 fw-semibold fs-7">Distribution by status</span>
                    </h3>
                </div>
                <div class="card-body">
                    <div class="chart-container" style="position: relative; height:400px;">
                        <canvas id="statusChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

<script>
    // Chart data from PHP
    const chartData = @json($chartData);

    document.addEventListener('DOMContentLoaded', function() {
        // Fallback for KTUtil if not defined
        if (typeof KTUtil === 'undefined') {
            window.KTUtil = {
                getCssVariableValue: function(variableName) {
                    const value = getComputedStyle(document.documentElement).getPropertyValue(
                        variableName);
                    return value && value.trim();
                }
            };
        }

        // Detect dark mode
        const isDarkMode = document.documentElement.getAttribute('data-theme') === 'dark' ||
            document.body.classList.contains('dark-mode') ||
            window.matchMedia('(prefers-color-scheme: dark)').matches;

        // Set theme colors based on mode
        const theme = {
            // Text colors
            textColor: '#3F4254',
            textMuted: '#7E8299',

            // Background colors
            tooltipBg: '#ffffff',
            tooltipBorder: '#E4E6EF',

            // Grid colors
            gridColor: '#F3F6F9',

            // Chart colors
            chartColors: {
                primary: '#3E97FF',
                primaryLight: 'rgba(62, 151, 255, 0.2)',
                success: '#50CD89',
                successLight: 'rgba(80, 205, 137, 0.2)',
                warning: '#F1BC00',
                danger: '#F1416C',
                info: '#7239EA'
            },

            // Service chart colors (vibrant in both modes)
            serviceColors: [
                '#3E97FF', // primary blue
                '#50CD89', // success green
                '#FFA800', // warning orange
                '#F1416C', // danger red
                '#7239EA', // purple
                '#0BB783', // teal
                '#F1BC00', // yellow
                '#181C32', // dark/light contrast
                '#8950FC', // indigo
                '#1BC5BD' // info
            ],

            // Status colors
            statusColors: [
                '#3E97FF', // scheduled - blue
                '#50CD89', // completed - green
                '#F1416C' // cancelled - red
            ]
        };

        // Initialize all charts with theme
        initMonthlyAppointmentsChart(theme);
        initRevenueChart(theme);
        initServicesChart(theme);
        initStatusChart(theme);
    });

    /*---------------------------------------------------------------------------
     * Chart 1: Monthly Appointments
     * Bar chart showing appointment counts by month
     *--------------------------------------------------------------------------*/
    function initMonthlyAppointmentsChart(theme) {
        const ctx = document.getElementById('appointmentsChart').getContext('2d');
        const data = chartData.monthlyAppointments;

        // Create gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, theme.chartColors.primary);
        gradient.addColorStop(1, theme.chartColors.primaryLight);

        // Define fonts
        const fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif') || 'Poppins, Helvetica, sans-serif';

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Appointments',
                    data: data.values,
                    backgroundColor: gradient,
                    borderColor: theme.chartColors.primary,
                    borderWidth: 0,
                    borderRadius: 6,
                    barThickness: 30,
                    maxBarThickness: 40
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: false,
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: theme.tooltipBg,
                        titleColor: theme.textColor,
                        bodyColor: theme.textColor,
                        borderColor: theme.tooltipBorder,
                        borderWidth: 1,
                        padding: 12,
                        usePointStyle: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        boxPadding: 3,
                        callbacks: {
                            label: function(context) {
                                return `Appointments: ${context.raw}`;
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: theme.textMuted
                        }
                    },
                    y: {
                        grid: {
                            drawBorder: false,
                            color: theme.gridColor,
                            borderDash: [3, 3],
                            lineWidth: 1
                        },
                        ticks: {
                            beginAtZero: true,
                            stepSize: 5,
                            font: {
                                size: 12
                            },
                            color: theme.textMuted
                        }
                    }
                },
                animation: {
                    duration: 1000
                }
            }
        });
    }

    /*---------------------------------------------------------------------------
     * Chart 2: Revenue Trends
     * Line chart showing revenue trends by month
     *--------------------------------------------------------------------------*/
    function initRevenueChart(theme) {
        const ctx = document.getElementById('revenueChart').getContext('2d');
        const data = chartData.monthlyRevenue;

        // Create gradient
        const gradient = ctx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, theme.chartColors.successLight);
        gradient.addColorStop(1, 'rgba(80, 205, 137, 0.01)');

        // Define fonts
        const fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif') || 'Poppins, Helvetica, sans-serif';

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: data.labels,
                datasets: [{
                    label: 'Revenue',
                    data: data.values,
                    backgroundColor: gradient,
                    borderColor: theme.chartColors.success,
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: theme.chartColors.success,
                    pointBorderColor: theme.tooltipBg,
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    pointHoverBackgroundColor: theme.chartColors.success,
                    pointHoverBorderColor: theme.tooltipBg,
                    pointHoverBorderWidth: 2
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: false,
                    },
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: theme.tooltipBg,
                        titleColor: theme.textColor,
                        bodyColor: theme.textColor,
                        borderColor: theme.tooltipBorder,
                        borderWidth: 1,
                        padding: 12,
                        usePointStyle: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        boxPadding: 3,
                        callbacks: {
                            label: function(context) {
                                return `Revenue: $${context.raw.toFixed(2)}`;
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index',
                },
                scales: {
                    x: {
                        grid: {
                            display: false,
                            drawBorder: false
                        },
                        ticks: {
                            font: {
                                size: 12
                            },
                            color: theme.textMuted
                        }
                    },
                    y: {
                        grid: {
                            drawBorder: false,
                            color: theme.gridColor,
                            borderDash: [3, 3],
                            lineWidth: 1
                        },
                        ticks: {
                            beginAtZero: true,
                            font: {
                                size: 12
                            },
                            color: theme.textMuted,
                            callback: function(value) {
                                return '$' + value;
                            }
                        }
                    }
                },
                animation: {
                    duration: 1000
                }
            }
        });
    }

    /*---------------------------------------------------------------------------
     * Chart 3: Service Popularity
     * Doughnut chart showing service popularity
     *--------------------------------------------------------------------------*/
    function initServicesChart(theme) {
        const ctx = document.getElementById('servicesChart').getContext('2d');
        const data = chartData.servicePopularity;

        // Define fonts
        const fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif') || 'Poppins, Helvetica, sans-serif';

        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: theme.serviceColors,
                    borderWidth: 0,
                    hoverOffset: 8,
                    borderRadius: 4
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: false,
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: fontFamily,
                                size: 13
                            },
                            color: theme.textColor
                        }
                    },
                    tooltip: {
                        backgroundColor: theme.tooltipBg,
                        titleColor: theme.textColor,
                        bodyColor: theme.textColor,
                        borderColor: theme.tooltipBorder,
                        borderWidth: 1,
                        padding: 12,
                        usePointStyle: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        boxPadding: 3,
                        callbacks: {
                            label: function(context) {
                                const percentage = Math.round((context.raw / data.values.reduce((a, b) =>
                                    a + b, 0)) * 100);
                                return `${context.label}: ${context.raw} bookings (${percentage}%)`;
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                cutout: '65%',
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }

    /*---------------------------------------------------------------------------
     * Chart 4: Appointment Status Distribution
     * Pie chart showing appointment status distribution
     *--------------------------------------------------------------------------*/
    function initStatusChart(theme) {
        const ctx = document.getElementById('statusChart').getContext('2d');
        const data = chartData.statusDistribution;

        // Define fonts
        const fontFamily = KTUtil.getCssVariableValue('--bs-font-sans-serif') || 'Poppins, Helvetica, sans-serif';

        new Chart(ctx, {
            type: 'pie',
            data: {
                labels: data.labels,
                datasets: [{
                    data: data.values,
                    backgroundColor: theme.statusColors,
                    borderWidth: 0,
                    borderRadius: 4,
                    hoverOffset: 8
                }]
            },
            options: {
                plugins: {
                    title: {
                        display: false,
                    },
                    legend: {
                        position: 'right',
                        labels: {
                            padding: 20,
                            usePointStyle: true,
                            pointStyle: 'circle',
                            font: {
                                family: fontFamily,
                                size: 13
                            },
                            color: theme.textColor
                        }
                    },
                    tooltip: {
                        backgroundColor: theme.tooltipBg,
                        titleColor: theme.textColor,
                        bodyColor: theme.textColor,
                        borderColor: theme.tooltipBorder,
                        borderWidth: 1,
                        padding: 12,
                        usePointStyle: true,
                        boxWidth: 10,
                        boxHeight: 10,
                        boxPadding: 3,
                        callbacks: {
                            label: function(context) {
                                const percentage = Math.round((context.raw / data.values.reduce((a, b) =>
                                    a + b, 0)) * 100);
                                return `${context.label}: ${context.raw} (${percentage}%)`;
                            }
                        }
                    }
                },
                responsive: true,
                maintainAspectRatio: false,
                animation: {
                    animateScale: true,
                    animateRotate: true,
                    duration: 1000
                }
            }
        });
    }
</script>
