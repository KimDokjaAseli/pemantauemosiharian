// Sidebar Toggle Functionality
const sidebarCollapse = document.getElementById('sidebarCollapse');
const sidebar = document.getElementById('sidebar');

if (sidebarCollapse && sidebar) {
    sidebarCollapse.addEventListener('click', () => {
        sidebar.classList.toggle('active');
    });
}

// Notification System
function showNotification(message) {
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.innerHTML = `
        <div class="notification-content">
            <p>${message}</p>
            <button class="btn btn-primary btn-sm" onclick="window.location.href='record.php'">Ya</button>
            <button class="btn btn-secondary btn-sm" onclick="this.parentElement.parentElement.remove()">Tidak</button>
        </div>
    `;
    document.body.appendChild(notification);
}

// Activity Tracking
let lastActivity = new Date();
const inactivityThreshold = 24 * 60 * 60 * 1000; // 24 hours

function checkActivity() {
    const now = new Date();
    const timeDiff = now - lastActivity;

    if (timeDiff > inactivityThreshold) {
        showNotification('Anda belum mencatat emosi Anda hari ini. Ingin mencatat sekarang?');
    }
}

// Track user activity
['mousemove', 'keypress'].forEach(event => {
    document.addEventListener(event, () => {
        lastActivity = new Date();
    });
});

// Check activity every hour
setInterval(checkActivity, 60 * 60 * 1000);

// Chart.js Configuration
const ctx = document.getElementById('emotionChart');
if (ctx) {
    const chartCtx = ctx.getContext('2d');
    const emotionChart = new Chart(chartCtx, {
        type: 'bar',
        data: {
            labels: [],
            datasets: [{
                label: 'Intensitas Emosi',
                data: [],
                backgroundColor: [
                    'rgba(54, 162, 235, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(75, 192, 192, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(255, 205, 86, 0.8)'
                ],
                borderColor: [
                    'rgba(54, 162, 235, 1)',
                    'rgba(255, 99, 132, 1)',
                    'rgba(75, 192, 192, 1)',
                    'rgba(153, 102, 255, 1)',
                    'rgba(255, 159, 64, 1)',
                    'rgba(255, 205, 86, 1)'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        color: '#e2e8f0'
                    }
                },
                title: {
                    display: true,
                    text: 'Tren Emosi Harian',
                    color: '#e2e8f0'
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 5,
                    ticks: {
                        stepSize: 1,
                        color: '#e2e8f0'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                },
                x: {
                    ticks: {
                        color: '#e2e8f0'
                    },
                    grid: {
                        color: 'rgba(255, 255, 255, 0.1)'
                    }
                }
            },
            tooltips: {
                enabled: true,
                mode: 'index',
                intersect: false,
                callbacks: {
                    label: function(tooltipItem, data) {
                        return 'Intensitas: ' + tooltipItem.value;
                    }
                }
            }
        }
    });

    // Fetch data for chart
    fetch('api/get_emotions.php')
        .then(response => response.json())
        .then(data => {
            const labels = data.map(item => item.date);
            const values = data.map(item => item.intensity);
            emotionChart.data.labels = labels;
            emotionChart.data.datasets[0].data = values;
            emotionChart.update();
        });
}

// Form Validation for Emotion Logging
const emotionForm = document.getElementById('emotionForm');
if (emotionForm) {
    emotionForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const submitBtn = this.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

        try {
            const formData = new FormData(this);
            const response = await fetch('api/save_emotion.php', {
                method: 'POST',
                body: formData
            });

            const result = await response.json();
            
            if (result.success) {
                // Show success message
                alert('Emosi berhasil disimpan!');
                
                // Show recommendations
                const recommendationsSection = document.getElementById('recommendationsSection');
                recommendationsSection.style.display = 'block';
                
                // Load activities for the selected emotion
                const emotionType = formData.get('emotion_type');
                const activitiesResponse = await fetch('api/get_activities.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'emotion_type=' + emotionType
                });
                
                const activitiesHtml = await activitiesResponse.text();
                document.getElementById('activityList').innerHTML = activitiesHtml;
            } else {
                throw new Error(result.message || 'Terjadi kesalahan saat menyimpan emosi');
            }
        } catch (error) {
            console.error('Error:', error);
            alert(error.message);
        } finally {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Simpan Emosi';
        }
    });
}
