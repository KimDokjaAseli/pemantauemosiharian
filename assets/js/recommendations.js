// Activity recommendations data
const activityRecommendations = {
    senang: [
        {
            activity: "Bermain musik atau alat musik favorit",
            description: "Meningkatkan mood positif dan kreativitas"
        },
        {
            activity: "Membaca buku favorit",
            description: "Membuat pikiran lebih tenang dan damai"
        },
        {
            activity: "Berkebun atau menanam tanaman",
            description: "Membuat suasana hati lebih tenang dan produktif"
        }
    ],
    cemas: [
        {
            activity: "Meditasi atau yoga ringan",
            description: "Membantu menenangkan pikiran dan mengurangi stres"
        },
        {
            activity: "Mendengarkan musik relaksasi",
            description: "Membantu menenangkan pikiran dan mengurangi kecemasan"
        },
        {
            activity: "Menggambar atau membuat karya seni",
            description: "Membantu mengekspresikan perasaan dan mengurangi kecemasan"
        }
    ],
    marah: [
        {
            activity: "Berolahraga atau berlari",
            description: "Membantu mengeluarkan energi negatif dan menenangkan pikiran"
        },
        {
            activity: "Mengambil waktu untuk bernafas dalam-dalam",
            description: "Membantu mengontrol emosi dan menenangkan diri"
        },
        {
            activity: "Menulis jurnal atau mengungkapkan perasaan",
            description: "Membantu mengelola emosi dan mengekspresikan perasaan"
        }
    ],
    sedih: [
        {
            activity: "Bercerita pada teman atau keluarga",
            description: "Membantu mengurangi beban dan merasa didukung"
        },
        {
            activity: "Membuat daftar hal-hal yang disyukuri",
            description: "Membantu mengubah fokus dan meningkatkan mood"
        },
        {
            activity: "Membuat karya seni atau menulis",
            description: "Membantu mengekspresikan perasaan dan mengurangi sedih"
        }
    ]
};

// Recommendation class
class Recommendation {
    constructor(activity, description) {
        this.activity = activity;
        this.description = description;
    }

    // Create recommendation card HTML
    createCard() {
        const card = document.createElement('div');
        card.className = 'activity-card';
        
        const html = `
            <div class="activity-header">
                <h4>${this.activity}</h4>
                <button onclick="copyToClipboard('${this.activity}')" class="copy-btn">Salin</button>
            </div>
            <p>${this.description}</p>
        `;
        
        card.innerHTML = html;
        return card;
    }
}

// RecommendationManager class
class RecommendationManager {
    constructor() {
        this.recommendations = {};
        this.initializeRecommendations();
    }

    initializeRecommendations() {
        // Convert data to Recommendation objects
        for (const [emotion, activities] of Object.entries(activityRecommendations)) {
            this.recommendations[emotion] = activities.map(activity => 
                new Recommendation(activity.activity, activity.description)
            );
        }
    }

    // Get recommendations for an emotion
    getRecommendations(emotion) {
        return this.recommendations[emotion] || [];
    }

    // Display recommendations
    displayRecommendations(emotion, containerId) {
        const container = document.getElementById(containerId);
        if (!container) return;

        // Clear existing recommendations
        container.innerHTML = '';

        // Get recommendations for the emotion
        const recommendations = this.getRecommendations(emotion);
        
        // Add recommendations to container
        recommendations.forEach(rec => {
            const card = rec.createCard();
            container.appendChild(card);
        });

        // Add animation
        addRecommendationAnimation(container);
    }
}

// Copy to clipboard function
function copyToClipboard(text) {
    navigator.clipboard.writeText(text)
        .then(() => {
            const button = event.target;
            const originalText = button.textContent;
            button.textContent = '✓ Tersalin!';
            button.style.backgroundColor = '#4CAF50';
            
            setTimeout(() => {
                button.textContent = originalText;
                button.style.backgroundColor = '';
            }, 2000);
        })
        .catch(err => console.error('Failed to copy text: ', err));
}

// Add recommendation animation
function addRecommendationAnimation(container) {
    const recommendations = container.querySelectorAll('.activity-card');
    recommendations.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(10px)';
        card.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        
        setTimeout(() => {
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });
}

// Initialize recommendation manager
const recommendationManager = new RecommendationManager();

// Function to get recommendations for emotion
function showRecommendations(emotion, containerId = 'recommendation-list') {
    recommendationManager.displayRecommendations(emotion, containerId);
}

// Export functions for use in other files
window.showRecommendations = showRecommendations;
window.copyToClipboard = copyToClipboard;
