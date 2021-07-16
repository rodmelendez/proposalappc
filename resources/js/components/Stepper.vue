<template>
    <v-stepper v-model="e1">
        <v-stepper-header>
            <div v-for="(item, key, index) in items">
                <v-stepper-step :complete="e1 > index + 1" :step="index + 1">{{ item }}</v-stepper-step>
                <v-divider></v-divider>
            </div>
        </v-stepper-header>

        <v-stepper-items>
            <v-stepper-content v-for="(item, key, index) in items" :step="index + 1">
                <v-card
                        class="mb-5"
                        color="grey lighten-1"
                        height="200px"
                ></v-card>

                <v-btn
                        color="primary"
                        @click="e1 = index + 1"
                >
                    Continuar
                </v-btn>

                <v-btn flat>Cancelar</v-btn>
            </v-stepper-content>

            <!--<v-stepper-content step="2">
                <v-card
                        class="mb-5"
                        color="grey lighten-1"
                        height="200px"
                ></v-card>

                <v-btn
                        color="primary"
                        @click="e1 = 3"
                >
                    Continue
                </v-btn>

                <v-btn flat>Cancel</v-btn>
            </v-stepper-content>

            <v-stepper-content step="3">
                <v-card
                        class="mb-5"
                        color="grey lighten-1"
                        height="200px"
                ></v-card>

                <v-btn
                        color="primary"
                        @click="e1 = 1"
                >
                    Continue
                </v-btn>

                <v-btn flat>Cancel</v-btn>
            </v-stepper-content>-->
        </v-stepper-items>
    </v-stepper>
</template>

<script>
    export default {
        name: "Stepper",
        data () {
            return {
                e1: 0
            }
        },
        props: {
            items: Object
        },
    }
</script>

<style scoped>

</style>