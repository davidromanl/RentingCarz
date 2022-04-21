<template>
    <v-card>
        <v-card-title>
            <h3 class="ma-auto">{{ match.homeTeam.name }} <v-chip>vs</v-chip> {{ match.awayTeam.name }}</h3>
        </v-card-title>
        <v-card-text>
            <v-row>
                <v-col>
                    <v-chip
                    class="float-end"
                    :color="match.status === 'SCHEDULED' ? 'success' : 'orange'"
                    >
                    {{ match.status === "SCHEDULED" ? "Programado" : "Finalizado" }}
                    </v-chip>
                    <strong>Fecha: {{ match.utcDate }}</strong>
                </v-col>
            </v-row>

            <v-row>
                <v-col class="text-center">
                    <p>Local:</p>
                    <v-avatar color="teal darken-2" size="40">
                        <h2 class="white--text">{{ match.score.fullTime.homeTeam || 0 }}</h2>
                    </v-avatar>
                    <h3 class="pa-2">{{ match.homeTeam.name }}</h3>
                    <v-chip color="amber" v-if="match.score.winner==='HOME_TEAM'">Ganador</v-chip>
                </v-col>
                <v-col class="text-center">
                    <p>Visitante:</p>
                    <v-avatar color="teal darken-2" size="40">
                        <h2 class="white--text">{{ match.score.fullTime.awayTeam || 0 }}</h2>
                    </v-avatar>
                    <h3 class="pa-2">{{ match.awayTeam.name }}</h3>
                    <v-chip color="amber" v-if="match.score.winner==='AWAY_TEAM'">Ganador</v-chip>
                </v-col>
            </v-row>

            <v-row class="my-0">
                <v-col class="py-0 text-center">
                    <v-chip v-if="match.score.winner==='DRAW'">Empate</v-chip>
                </v-col>
            </v-row>

            <v-row>
                <v-col>
                    <h3>Árbitros</h3>
                </v-col>
            </v-row>
          <v-simple-table>
            <template v-slot:default>
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Role</th>
                        <th>Nacionalidad</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="r,i in match.referees" :key="i">
                        <td>{{ r.name }}</td>
                        <td>{{ r.role }}</td>
                        <td>{{ r.nationality }}</td>
                    </tr>
                </tbody>
            </template>
          </v-simple-table>
        </v-card-text>
        <v-card-actions>
            <v-btn small @click="$emit('close')" color="secondary"> Cerrar </v-btn>
            <v-spacer />
            <v-btn small @click="$emit('save')" color="success darken-2"> Guardar </v-btn>
        </v-card-actions>
    </v-card>
</template>

<script>

export default {
  name: 'MatchDetails',
  props: ['match'],
}
</script>
