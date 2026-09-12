<template>
    <transition name="modal-fade">
        <div class="mx-auto modal-backdrop">
            <div class="mx-auto modal" role="dialog" aria-labelledby="modalTitle" aria-describedby="modalDescription">
                <header class="modal-header bg-light d-flex justify-content-between align-items-center" id="modalTitle">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0 modal-title">{{ game.away_team }} vs. {{ game.home_team }}</h5><small
                            class="ms-1 text-muted">({{ game.global_game_id }})</small>
                    </div>
                    <button type="button" class="btn-close d-flex justify-content-between align-items-center" @click="close"
                        aria-label="Close modal">X</button>
                </header>
                <section class="modal-body" id="modalDescription">
                    <div id="stadium-details">
                        <h4 class="border-bottom">Details</h4>
                        <ul class="list-unstyled pl-0">
                            <li><strong>Stadium:</strong> {{ game.stadium_name }}</li>
                            <li><strong>Type:</strong> {{ game.stadium_type }}</li>
                            <li><strong>Playing Surface:</strong> {{ game.playing_surface }}</li>
                            <li><strong>Watch On:</strong> {{ game.channel }}</li>
                        </ul>
                    </div>
                    <div id="weather">
                        <h4>Forecasted Weather</h4>
                    </div>
                    <div id="odds">
                        <h4>Betting Odds</h4>
                    </div>
                </section>
                <footer class="modal-footer bg-light d-flex justify-content-start align-items-center">
                    <button type="button" class="btn btn-primary"
                        @click="$emit('submit-from-modal', pricingData)">Submit</button>
                    <button type="button" class="btn btn-link" @click="close" aria-label="Close modal">Close</button>
                </footer>
            </div>
        </div>
    </transition>
</template>

<script>
export default {
    name: "Modal",
    props: {
        game: Object
    },
    methods: {
        close() {
            this.$emit("close");
        }
    }
};
</script>

<style scoped>
.modal-backdrop {
    position: fixed;
    top: 0;
    bottom: 0;
    left: 0;
    right: 0;
    background-color: rgba(0, 0, 0, 0.5);
}

.modal {
    background: #FFFFFF;
    box-shadow: 2px 2px 20px 1px;
    overflow-x: auto;
    display: flex;
    flex-direction: column;
    width: 570px;
    height: auto;
    position: relative;
    top: 25px;
    border-radius: 5px;
}

.modal-header,
.modal-footer {
    padding: 10px 15px;
}

.modal-footer {
    border-top: 1px solid #eeeeee;
}

.modal-body {
    position: relative;
    padding: 10px;
}

.btn-close {
    font-size: 20px;
    padding: 10px;
    cursor: pointer;
    font-weight: bold;
    background: transparent;
    border: none;
}

.btn-green {
    color: white;
    background: #4AAE9B;
    border: 1px solid #4AAE9B;
    border-radius: 2px;
}

.modal-fade-enter,
.modal-fade-leave-to {
    opacity: 0;
}

.modal-fade-enter-active,
.modal-fade-leave-active {
    transition: opacity .5s ease;
}
</style>