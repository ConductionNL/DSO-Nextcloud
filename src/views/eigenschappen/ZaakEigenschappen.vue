<script setup>
import { zaakStore } from '../../store/store.js'
</script>

<template>
	<div>
		<div v-if="!loading">
			<NcListItem v-for="(zaak, i) in zakenList"
				:key="`${zaak}${i}`"
				:name="zaak?.zaakStore"
				:active="zaakStore.zakenItem?.id === zaak?.id"
				:details="'1h'"
				:counter-number="44"
				:force-display-actions="true"
				@click="toggleZaakEigenschap(zaak)">
				<template #icon>
					<BriefcaseAccountOutline :class="zaakStore.zakenItem === zaak.id && 'selectedZaakIcon'"
						disable-menu
						:size="44" />
				</template>
				<template #subname>
					{{ zaak?.summary }}
				</template>
				<template #actions>
					<NcActionButton>
						Button one
					</NcActionButton>
					<NcActionButton>
						Button two
					</NcActionButton>
					<NcActionButton>
						Button three
					</NcActionButton>
				</template>
			</NcListItem>
		</div>

		<div v-if="!zakenList?.length && !loading">
			Geen eigenschappen gevonden.
		</div>

		<NcLoadingIcon v-if="loading"
			class="loadingIcon"
			:size="64"
			appearance="dark"
			name="Zaken aan het laden" />
	</div>
</template>
<script>
import { NcListItem, NcActionButton, NcLoadingIcon } from '@nextcloud/vue'
import BriefcaseAccountOutline from 'vue-material-design-icons/BriefcaseAccountOutline.vue'

export default {
	name: 'ZaakEigenschappen',
	components: {
		NcListItem,
		NcActionButton,
		BriefcaseAccountOutline,
		NcLoadingIcon,
	},
	props: {
		zaakId: {
			type: String,
			required: true,
		},
	},
	data() {
		return {
			search: '',
			loading: false,
			zakenList: [],
		}
	},
	watch: {
		zaakId(newVal) {
			this.fetchData(newVal)
		},
	},
	mounted() {
		this.fetchData(this.zaakId)
	},
	methods: {
		fetchData(zaakId) {
			this.loading = true
			fetch(
				'/index.php/apps/zaakafhandelapp/api/zrc/zaken/' + zaakId + '/eigenschappen',
				{
					method: 'GET',
				},
			)
				.then((response) => {
					response.json().then((data) => {
						this.zakenList = data.results || []
					})
					this.loading = false
				})
				.catch((err) => {
					console.error(err)
					this.loading = false
				})
		},
		toggleZaakEigenschap(zaakEigenschap) {
			// TODO: toggle zaakEigenschap
		},
		clearText() {
			this.search = ''
		},
	},
}
</script>
<style>
.listHeader {
    position: sticky;
    top: 0;
    z-index: 1000;
    background-color: var(--color-main-background);
    border-bottom: 1px solid var(--color-border);
}

.searchField {
    padding-inline-start: 65px;
    padding-inline-end: 20px;
    margin-block-end: 6px;
}

.selectedZaakIcon>svg {
    fill: white;
}

.loadingIcon {
    margin-block-start: var(--zaa-margin-20);
}
</style>
