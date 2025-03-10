<template>
  <div class="container">
    <h1>Gruplar</h1>
    <button @click="generateGroups" class="generate-btn" :disabled="loading">
      {{ loading ? 'Oluşturuluyor...' : 'Grupları Oluştur' }}
    </button>

    <div class="groups-container">
      <div v-for="group in groups" :key="group.name" class="group-card">
        <h2>{{ group.name }}</h2>
        <ul>
          <li v-for="team in group.teams" :key="team">{{ team }}</li>
        </ul>
      </div>
    </div>

    <!-- Takımları Gruplara Ata Butonu -->
    <button v-if="groups.length > 0" @click="assignTeams" class="assign-btn">
      Takımları Gruplara Ata
    </button>

    <!-- Fikstür Oluştur Butonu -->
    <transition name="slide">
      <button v-if="teamsAssigned" @click="goToFixtures" class="fixture-btn">
        Fikstür Oluştur
      </button>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const groups = ref([]);
const loading = ref(false);
const assigning = ref(false);
const teamsAssigned = ref(false);
const router = useRouter();

const generateGroups = async () => {
  loading.value = true;

  try {
    await axios.post('/api/generate-groups');
  } catch (error) {
    console.error('Gruplar oluşturulurken hata:', error);
  } finally {
    loading.value = false;
    fetchGroups();
  }
};

// Takımları gruplara atama ve güncelleme
const assignTeams = async () => {
  assigning.value = true;

  try {
    await axios.post('/api/assign-teams');
    alert("Takımlar gruplara atandı!");
    fetchGroups(); // Güncellenmiş grupları çek
    teamsAssigned.value = true; // Takımlar atandıktan sonra butonu göster
  } catch (error) {
    console.error('Takımları gruplara atarken hata oluştu:', error);
  } finally {
    assigning.value = false;
  }
};

// Grupları ve takımları veritabanından çek
const fetchGroups = async () => {
  try {
    const response = await axios.get('/api/groups-with-teams');
    groups.value = response.data;
    if (groups.value.some(group => group.teams.length > 0)) {
      teamsAssigned.value = true; // Eğer takımlar atanmışsa butonu göster
    }
  } catch (error) {
    console.error('Grupları çekerken hata oluştu:', error);
  }
};

// Fikstür sayfasına geçiş yap
const goToFixtures = () => {
  router.push('/fixtures');
};

// Sayfa yüklendiğinde grupları çek ve her 3 saniyede bir güncelle
onMounted(() => {
  fetchGroups();
  setInterval(fetchGroups, 3000);
});
</script>

<style scoped>
.container {
  text-align: center;
  padding: 20px;
}

.generate-btn, .assign-btn, .fixture-btn {
  background: #007bff;
  color: white;
  border: none;
  padding: 12px 24px;
  cursor: pointer;
  margin-top: 30px;
  font-size: 18px;
  border-radius: 5px;
  transition: 0.3s;
  display: block;
  margin-left: auto;
  margin-right: auto;
}

.generate-btn:hover, .assign-btn:hover, .fixture-btn:hover {
  background: #0056b3;
}

.groups-container {
  display: flex;
  flex-wrap: wrap;
  justify-content: center;
  gap: 20px;
  margin-top: 20px;
}

.group-card {
  background: #333;
  color: white;
  padding: 15px;
  border-radius: 8px;
  width: 200px;
  text-align: center;
  font-size: 18px;
}

.group-card ul {
  list-style: none;
  padding: 0;
  margin-top: 10px;
}

.group-card li {
  font-size: 16px;
  background: #444;
  margin: 5px 0;
  padding: 5px;
  border-radius: 5px;
}

/* Geçiş Animasyonu */
.slide-enter-active, .slide-leave-active {
  transition: transform 0.5s ease-in-out, opacity 0.5s;
}
.slide-enter-from {
  transform: translateY(20px);
  opacity: 0;
}
.slide-leave-to {
  transform: translateY(-20px);
  opacity: 0;
}
</style>
