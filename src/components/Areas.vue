<template>
  <v-container>
    <v-row class="text-center">
      <template v-for="c in competitionsSelected">
        <v-expand-transition
          group
          :key="c.id"
          mode="in-out"
          hide-on-leave
          leave-absolute
        >
          <v-col cols="12" md="3">
            <v-card
              class="fill-height"
              :color="c.id === competition.id ? 'teal darken-3' : ''"
              dark
              @click="() => getMatches(c.id)"
            >
              <v-card-text>
                <v-avatar size="64" color="grey darken-3">
                  <v-img v-if="c.emblemUrl" :src="c.emblemUrl"></v-img>
                  <v-icon v-else dark>mdi-image</v-icon>
                </v-avatar>
                <v-divider class="my-2" />
                <h3>{{ c.name }}</h3>
              </v-card-text>
            </v-card>
          </v-col>
        </v-expand-transition>
      </template>
    </v-row>
    <v-row>
      <v-col cols="auto">
        <v-avatar>
          <v-img v-if="avatar" :src="avatar.area.ensignUrl"></v-img>
        </v-avatar>
      </v-col>
      <v-col>
        <v-select label="Pais" v-model="country" :items="countrys"> </v-select>
      </v-col>
      <v-col>
        <v-text-field
          v-model="dateFrom"
          label="Fecha inicio"
          :rules="[(v) => !!v || 'Requerido.']"
          type="date"
          @change="changeDate()"
          :error-messages="
            new Date(dateFrom) > new Date(dateTo)
              ? 'Debe ser anterior a la fecha final.'
              : ''
          "
        ></v-text-field>
      </v-col>
      <v-col>
        <v-text-field
          v-model="dateTo"
          label="Fecha final"
          type="date"
          :rules="[(v) => !!v || 'Requerido.']"
          @change="changeDate()"
        ></v-text-field>
      </v-col>
    </v-row>
    <v-row>
      <v-col>
        <div v-if="Object.keys(competition).length">
          <strong>Liga:</strong> {{ competition.name }} -
          <strong>Actualizado:</strong> {{ competition.lastUpdated }}
        </div>
      </v-col>
      <v-col>
        <small v-if="count" class="float-end">
          <v-icon small left>mdi-eye</v-icon>{{fromToPage}}</small>
        <h3>Resultados: {{count}}</h3>
      </v-col>
    </v-row>
    <hr class="mb-2" />
    <v-row>
      <v-col v-if="!matches.length">
        <p>No se encontraron resultados</p>
      </v-col>
      <v-col v-else cols="12" md="3" v-for="m in matchesSelected" :key="m.id">
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
    <v-row>
      <v-col>
        <v-pagination dark
          v-if="count>12"
          v-model="page"
          :length="lengthPage"
        ></v-pagination>
      </v-col>
    </v-row>

    <v-dialog scrollable max-width="600" v-model="dialog">
      <v-expand-transition>
        <MatchDetails @save="saveMatch" @close="dialog=false" v-if="dialog" :match="match"/>
      </v-expand-transition>
    </v-dialog>

    <v-snackbar color="teal" v-model="saved">
      Partido guardado
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="saved = false"> Cerrar </v-btn>
      </template>
    </v-snackbar>
    <v-snackbar color="error" v-model="error">
      Token API no tiene permisos en esta liga
      <template v-slot:action="{ attrs }">
        <v-btn text v-bind="attrs" @click="error = false"> Cerrar </v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script>
import axios from "axios";
import MatchDetails from '@/components/MatchDetails.vue'

const formatDate = (v) => {
  const date = new Date(v);
  const day = date.getDate() <= 9 ? "0" + date.getDate() : date.getDate();
  const m = date.getMonth() + 1;
  const month = m <= 9 ? "0" + m : m;
  const year = date.getFullYear();
  return `${year}-${month}-${day}`;
};

const today = new Date()

const config = { headers: { "X-Auth-Token": "bddd65ee5161464f9fd0190babfa3492" } }

export default {
  name: "Areas",

  components: { MatchDetails },

  data: () => ({
    competitions: [],
    country: null,
    dateTo: formatDate(today),
    dateFrom: formatDate(today.setMonth(today.getMonth() - 1)),
    //avatar: null
    error: false,
    dialog: false,
    page: 1,
    matches: [],
    count: 1,
    competition: {},
    match: {},
    saved: false
  }),

  created() {
    this.getData();
  },

  computed: {
    countrys() {
      return this.competitions
        .map(({ area }) => area.name)
        .filter((value, index, self) => self.indexOf(value) === index);
    },
    lengthPage() {
      return Math.ceil(this.matches.length / 12)
    },
    lastMatchPage() {
      return 12 * this.page
    },
    fromToPage() {
      const from = this.page > 1 ? this.lastMatchPage - 12 : 1
      const to = this.count >= 12 && this.lastMatchPage <= this.count
        ? this.lastMatchPage
        : this.count
      return `de ${from} a ${to}`
    },
    matchesSelected() {
      return this.matches.slice(this.lastMatchPage - 12,this.lastMatchPage)
    },
    competitionsSelected() {
      return this.competitions.filter(({ area }) => area.name === this.country);
    },
    avatar() {
      let a = this.competitions.find(({ area }) => area.name === this.country);
      return a || null;
    },
  },

  methods: {
    async getData() {
      try {
        const baseURL = "https://api.football-data.org/v2/competitions/";
        const { data } = await axios.get(baseURL + "?areas=2077", config);
        this.competitions = data.competitions;
        this.country = this.competitions[0].area.name;
      } catch (error) {
        this.error = true;
        console.log(error);
      }
    },
    async getMatches(id) {
      try {
        const baseURL = "https://api.football-data.org/v2/competitions/";
        const url = "dateFrom=" + this.dateFrom + "&dateTo=" + this.dateTo;
        const { data } = await axios.get(`${baseURL}${id}/matches?${url}`, config);
        this.count = data.count;
        this.matches = data.matches;
        this.competition = data.competition;
        this.page = 1;
      } catch (error) {
        this.error = true;
        console.log(error);
      }
    },

    changeDate() {
      return this.competition.id && this.getMatches(this.competition.id);
    },

    openMatch(m) {
      this.dialog = true;
      this.match = m;
    },

    async saveMatch(m = null) {
      if(m) this.match = m;
      try {
        const baseURL = "../api/index.php";
        const match = { 
          id: this.match.id, 
          utcDate: this.match.utcDate,
          status: this.match.status,
          score: JSON.stringify(this.match.score),
          homeTeam: JSON.stringify(this.match.homeTeam),
          awayTeam: JSON.stringify(this.match.awayTeam),
          referees: JSON.stringify(this.match.referees),
        }
        const { data } = await axios.post(baseURL + '/matches', match);
        console.log(data);
        if(!m) this.dialog = false;
        this.saved = true
      } catch (error) {
        this.error = true;
        console.log(error);
      }
    },

  },
};
</script>
