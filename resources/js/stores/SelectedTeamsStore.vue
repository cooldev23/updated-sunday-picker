<script>
    import { defineStore } from "pinia";
    import { ref, computed } from 'vue';
    
    export const useSelectedTeamsStore = defineStore('selectedTeams', () => {
      const userSelectedTeams = ref([]);
      const areGamesDisabled = ref(false);
      const weekGamesCount = ref([]);
      
      const tiebreaker = computed(() => {
        let tiebreak = userSelectedTeams.value.filter( (entry) => entry.tiebreaker !== null );
        return tiebreak.length ? tiebreak[0] : 0;
      });

      function hydrateSelectedTeams(payload) {
        userSelectedTeams.value = payload;
      }

      function updateSelectedTeams(payload) {
        if (userSelectedTeams.value.length) {
          for (const item of userSelectedTeams.value) {
            if (payload.game === item.gameId) {
              item.team = payload.team;
              item.weight = payload.weight;
              return;
            }
          }
          userSelectedTeams.value.push({
            team: payload.team,
            gameId: payload.game,
            weight: payload.weight
          })
        } else {
          userSelectedTeams.value.push({
            team: payload.team,
            gameId: payload.game,
            weight: payload.weight
          })
        }
      }

      function resetSelectedPicks() {
        userSelectedTeams.value = [];
      }

      function setWeekGamesCount(payload) {
        for (let i = 0; i <= payload; i++) {
          weekGamesCount.value.push(i);
        }
      }
      
      return {userSelectedTeams, hydrateSelectedTeams, tiebreaker, areGamesDisabled, updateSelectedTeams, resetSelectedPicks, weekGamesCount, setWeekGamesCount}
    })
</script>