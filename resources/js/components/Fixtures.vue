<template>
  <div class="container">
    <!-- Sol Panel: Fikstür -->
    <div class="left-panel">
    <button @click="generateFixtures" class="generate-btn">
        {{ loading ? 'Fikstür Oluşturuluyor...' : 'Fikstürü Oluştur' }}
    </button>
      <h2>Fikstür</h2>
      <ul v-if="fixtures.length > 0">
        <li v-for="match in fixtures" :key="match.id">
          <strong>Grup Maçı:</strong> 
          {{ match.home_team }} - {{ match.away_team }}
          <span v-if="match.played">
            ({{ match.home_score }} - {{ match.away_score }})
          </span>
        </li>
      </ul>
      <p v-else>Henüz fikstür oluşturulmadı.</p>

      
    </div>

    <!-- Sağ Panel: Maçları Oyna Butonu ve Sonuçlar -->
    <div class="right-panel">
      <button @click="simulateMatches" class="play-btn" :disabled="loading">
        {{ loading ? 'Maçlar Oynanıyor...' : 'Maçları Oyna' }}
      </button>

      <h2>Maç Sonuçları</h2>
      <ul v-if="matchResults.length > 0">
        <li v-for="match in matchResults" :key="match.id">
          <strong>GM Sonuç:</strong> 
          {{ match.home_team }} {{ match.home_score }} - {{ match.away_score }} {{ match.away_team }}
        </li>
      </ul>
      <p v-else>Maçları oyna ve tekrar butona basıp sonuçları gör.</p>
    </div>
  </div>

    <transition name="slide">
        <button @click="calculateAndGoToGroupStandings" class="next-btn">
            Grup Sıralamalarını Göster
        </button>
    </transition>

    



</template>

<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import { useRouter } from 'vue-router';

const router = useRouter();

const calculateAndGoToGroupStandings = async () => {
  try {
    console.log("Grup sıralamalarını hesaplama başlatıldı..."); // Hata ayıklamak için

    await axios.get('/api/calculate-and-fetch-group-standings');

    console.log("Sıralamalar hesaplandı, yönlendirme başlatılıyor...");
    
    router.push('/group-standings');
  } catch (error) {
    console.error('Grup sıralamalarını hesaplama hatası:', error);
  }
};




const fixtures = ref([]);
const matchResults = ref([]);
const loading = ref(false);

// Fikstürü getir
const fetchFixtures = async () => {
  try {
    const response = await axios.get('/api/fixtures');
    fixtures.value = response.data;
  } catch (error) {
    console.error('Fikstürü çekerken hata oluştu:', error);
  }
};

// Fikstürü oluştur
const generateFixtures = async () => {
  loading.value = true;
  try {
    const response = await axios.post('/api/generate-fixtures');
    console.log(response.data); // Konsolda API cevabını göster
    await fetchFixtures(); // Fikstürü hemen güncelle
  } catch (error) {
    console.error('Fikstür oluşturulurken hata oluştu:', error);
  } finally {
    loading.value = false;
  }
};

// Maçları simüle et ve sonuçları güncelle
const simulateMatches = async () => {
  loading.value = true;
  try {
    // Önce maçları simüle et
    await axios.post('/api/simulate-matches');

    // Maçları simüle ettikten HEMEN SONRA sonuçları getir
    await fetchMatchResults(); 
  } catch (error) {
    console.error('Maçları oynatırken hata oluştu:', error);
  } finally {
    loading.value = false;
  }
};


// Oynanmış maçları getir
const fetchMatchResults = async () => {
  try {
    const response = await axios.get('/api/match-results');
    matchResults.value = response.data;
  } catch (error) {
    console.error('Maç sonuçlarını çekerken hata oluştu:', error);
  }
};

// Sayfa yüklendiğinde fikstürü getir
onMounted(() => {
  fetchFixtures();
  fetchMatchResults();
});
</script>

<style scoped>
.container {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  padding: 20px;
  gap: 20px;
}

.left-panel {
  width: 50%;
  background: #f8f9fa;
  padding: 20px;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.right-panel {
  width: 50%;
  background: #e9ecef;
  padding: 20px;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  align-items: center;
}

.generate-btn, .play-btn {
  background: #007bff;
  color: white;
  border: none;
  padding: 12px 24px;
  cursor: pointer;
  font-size: 18px;
  border-radius: 5px;
  transition: 0.3s;
  margin-bottom: 20px;
}

.generate-btn:hover {
  background: #0056b3;
}

.play-btn {
  background: #28a745;
}

.play-btn:hover {
  background: #218838;
}

.play-btn:disabled {
  background: #6c757d;
  cursor: not-allowed;
}

ul {
  list-style: none;
  padding: 0;
  width: 100%;
}

li {
  font-size: 18px;
  margin: 10px 0;
  padding: 5px;
  background: white;
  border-radius: 5px;
  text-align: center;
}
</style>
