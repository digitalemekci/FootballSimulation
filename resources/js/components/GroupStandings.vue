<template>
  <div class="container">
    <h1>Grup Sıralamaları</h1>
    <div v-if="groupedStandings.length > 0">
      <div v-for="(group, index) in groupedStandings" :key="index" class="group">
        <h2>Grup {{ index }}</h2>
        <table>
          <thead>
            <tr>
              <th>Takım</th>
              <th>Av.</th>
              <th>G</th>
              <th>B</th>
              <th>M</th>
              <th>P</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="team in group" :key="team.team_name">
              <td>{{ team.team_name }}</td>
              <td>{{ team.goal_difference }}</td>
              <td>{{ team.wins }}</td>
              <td>{{ team.draws }}</td>
              <td>{{ team.losses }}</td>
              <td><strong>{{ team.points }}</strong></td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
    <p v-else>Henüz sıralama bulunmuyor.</p>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue';
import axios from 'axios';

const standings = ref([]);

const fetchGroupStandings = async () => {
  try {
    const response = await axios.get('/api/calculate-and-fetch-group-standings');
    standings.value = response.data;
  } catch (error) {
    console.error('Grup sıralamalarını çekerken hata oluştu:', error);
  }
};

onMounted(() => {
  fetchGroupStandings();
});


// Grupları bir objede gruplayarak göstermek için
const groupedStandings = computed(() => {
  return standings.value.reduce((acc, team) => {
    if (!acc[team.group_name]) {
      acc[team.group_name] = [];
    }
    acc[team.group_name].push(team);
    return acc;
  }, {});
});
</script>

<style scoped>
.container {
  padding: 20px;
  text-align: center;
}

.group {
  margin-bottom: 20px;
}

table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 10px;
}

th, td {
  border: 1px solid #ddd;
  padding: 8px;
  text-align: center;
}

th {
  background-color: #007bff;
  color: white;
}

.group-btn {
  background: #ffc107;
  color: black;
  border: none;
  padding: 12px 24px;
  cursor: pointer;
  font-size: 18px;
  border-radius: 5px;
  transition: 0.3s;
  margin-top: 20px;
}

.group-btn:hover {
  background: #e0a800;
}
</style>
