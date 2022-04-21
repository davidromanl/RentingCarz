<template>
  <v-container>
    <v-row>
      <v-col v-if="!matches.length">
        <p>No se encontraron resultados</p>
      </v-col>
      <v-col v-else cols="12" md="3" v-for="m in matches" :key="m.id">
        <v-card dark color="blue-grey darken-3" class="fill-height d-flex flex-column">
          <v-card-text>
            <v-row>
              <v-col class="py-1">
                <v-chip
                  x-small
                  class="float-end"
                  :color="m.status === 'SCHEDULED' ? 'success' : 'orange'"
                >
                  {{ m.status === "SCHEDULED" ? "Programado" : "Finalizado" }}
                </v-chip>
                <small>Fecha:</small>
                {{ m.utcDate }}
              </v-col>
            </v-row>
            <v-row>
              <v-col>
                <v-avatar class="float-end mt-2" color="teal darken-2" size="40">
                  <h2>{{ m.score.fullTime.homeTeam || 0 }}</h2>
                </v-avatar>
                <small>Local:</small>
                <h3>{{ m.homeTeam.name }}</h3>
              </v-col>
            </v-row>
            <v-divider class="my-2" />
            <v-row>
              <v-col class="text-right">
                <v-avatar class="float-start mt-2" color="teal darken-2" size="40">
                  <h2>{{ m.score.fullTime.awayTeam || 0 }}</h2>
                </v-avatar>
                <small>Visitante:</small>
                <h3>{{ m.awayTeam.name }}</h3>
              </v-col>
            </v-row>
          </v-card-text>
          <v-divider />
          <v-spacer></v-spacer>
          <v-card-actions>
            <v-btn small color="primary darken-2" @click="openMatch(m)"> Ver Info </v-btn>
            <v-spacer />
            <v-btn small color="success darken-2" @click="saveMatch(m)"> Guardar </v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    
    <v-dialog scrollable max-width="600" v-model="dialog">
      <v-expand-transition>
        <MatchDetails @save="saveMatch" @close="dialog=false" v-if="dialog" :match="match"/>
      </v-expand-transition>
    </v-dialog>


  </v-container>
</template>

<script>
import axios from "axios";
import MatchDetails from '@/components/MatchDetails.vue'

export default {
  name: 'Saved',

  components: { MatchDetails },

  data: () => ({
    matches: [],
    match: null,
    dialog: false,
  }),

  created() {
    this.getData();
  },

  methods: {
    async getData() {
      try {
        let matches = []
        const baseURL = "../api/index.php";
        const { data } = await axios.get(baseURL + "/matches/saved");
        
        for(let i in data) {
          const match = { id: data[i].id };
          match.utcDate  = data[i].utcDate;
          match.status   = data[i].status;
          match.score    = JSON.parse(data[i].score);
          match.homeTeam = JSON.parse(data[i].homeTeam);
          match.awayTeam = JSON.parse(data[i].awayTeam);
          match.referees = JSON.parse(data[i].referees);
          matches.push(match);
        }
        this.matches = matches;
        console.log(data);
      } catch (error) {
        console.log(error);
      }
    },
    openMatch(m) {
      this.dialog = true;
      this.match = m;
    },
    saveMatch(m = null) {
      if(!m) this.dialog = false;
    }
  },
}
</script>
