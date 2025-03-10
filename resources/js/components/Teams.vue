<template>
  <div class="container">
    <h1>Şampiyonlar Ligi Simülasyonu</h1>
    <button @click="importTeams" :disabled="loading">
      {{ loading ? 'Yükleniyor...' : 'Takımları Yükle' }}
    </button>
    <p v-if="message">{{ message }}</p>

    <ul v-if="teams.length > 0">
      <li v-for="team in teams" :key="team.id">
        {{ team.name }}
      </li>
    </ul>

    <!-- Geçiş Butonu -->
    <transition name="slide">
      <button v-if="teams.length > 0" @click="goToGroups" class="next-btn">
        Grupları Oluştur
      </button>
    </transition>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useRouter } from 'vue-router';
import axios from 'axios';

const teams = ref([]);
const message = ref('');
const loading = ref(false);
const router = useRouter();

const importTeams = async () => {
  loading.value = true;
  message.value = 'Takımlar yükleniyor...';

  try {
    await axios.post('/api/import-teams');
    message.value = 'Takımlar yükleme işlemi başlatıldı!';
  } catch (error) {
    message.value = 'Hata oluştu: ' + (error.response?.data?.error || error.message);
    console.error(error);
  } finally {
    loading.value = false;
  }
};

// Takımları veritabanından çek ve ekrana yaz
const fetchTeams = async () => {
  try {
    const response = await axios.get('/api/teams');
    teams.value = response.data;
  } catch (error) {
    console.error('Takımları çekerken hata oluştu:', error);
  }
};

// Sayfa yüklendiğinde takımları çek
onMounted(() => {
  fetchTeams();
  setInterval(fetchTeams, 3000);
});

// Grup oluşturma sayfasına yönlendirme
const goToGroups = () => {
  router.push('/groups');
};
</script>

<style scoped>
.container {
  text-align: center;
  padding: 20px;
}
button {
  background: #007bff;
  color: white;
  border: none;
  padding: 10px 20px;
  cursor: pointer;
  margin-bottom: 20px;
}
ul {
  list-style: none;
  padding: 0;
}
li {
  font-size: 18px;
  margin: 5px 0;
}
p {
  font-size: 18px;
  margin-top: 10px;
}

/* Geçiş Butonu */
.next-btn {
  display: block;
  margin: 20px auto;
  padding: 10px 20px;
  background: #28a745;
  color: white;
  font-size: 16px;
  border-radius: 5px;
  transition: 0.3s ease-in-out;
}
.next-btn:hover {
  background: #218838;
}

/* Kaydırmalı Animasyon */
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
