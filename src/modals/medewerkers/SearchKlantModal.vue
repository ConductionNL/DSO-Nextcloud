<script setup>
import { klantStore } from '../../store/store.js'
</script>

<template>
	<NcDialog
		:name="startingType === 'persoon' ? 'Persoon zoeken' : 'Organisatie zoeken'"
		size="normal"
		label-id="searchKlantModal"
		dialog-classes="SearchKlantModal"
		:close-on-click-outside="false"
		@closing="closeModalFromButton()">
		<div class="listContainer">
			<div class="filtersContainer">
				<NcCheckboxRadioSwitch v-if="startingType === 'persoon'"
					:checked.sync="klantenSearchType"
					value="geboortedatum_achternaam"
					name="klantenSearchType"
					type="radio">
					Geboortedatum + achternaam
				</NcCheckboxRadioSwitch>
				<NcCheckboxRadioSwitch v-if="startingType === 'persoon'"
					:checked.sync="klantenSearchType"
					value="bsn"
					name="klantenSearchType"
					type="radio">
					BSN
				</NcCheckboxRadioSwitch>
				<NcCheckboxRadioSwitch v-if="startingType === 'organisatie'"
					:checked.sync="klantenSearchType"
					value="bedrijfsnaam"
					name="klantenSearchType"
					type="radio">
					Bedrijfsnaam
				</NcCheckboxRadioSwitch>
				<NcCheckboxRadioSwitch v-if="startingType === 'organisatie'"
					:checked.sync="klantenSearchType"
					value="kvkNummer"
					name="klantenSearchType"
					type="radio">
					KVK nummer
				</NcCheckboxRadioSwitch>
				<NcCheckboxRadioSwitch :checked.sync="klantenSearchType"
					value="postcode_huisnummer"
					name="klantenSearchType"
					type="radio">
					Postcode + huisnummer
				</NcCheckboxRadioSwitch>
				<NcCheckboxRadioSwitch :checked.sync="klantenSearchType"
					value="emailadres"
					name="klantenSearchType"
					type="radio">
					Emailadres
				</NcCheckboxRadioSwitch>
				<NcCheckboxRadioSwitch :checked.sync="klantenSearchType"
					value="telefoonnummer"
					name="klantenSearchType"
					type="radio">
					Telefoonnummer
				</NcCheckboxRadioSwitch>
			</div>
		</div>

		<div class="searchContainer">
			<div v-if="klantenSearchType === 'geboortedatum_achternaam'" class="flex">
				<NcDateTimePicker v-model="searchQuery_geboortedatum"
					:disabled="loading"
					class="date-picker" />

				<NcTextField :disabled="loading"
					label="achternaam"
					maxlength="255"
					class="searchField"
					:value.sync="searchQuery" />
			</div>
			<div v-else>
				<NcTextField :disabled="loading"
					:label="searchLabel"
					maxlength="255"
					class="searchField"
					:value.sync="searchQuery" />
			</div>

			<NcButton type="primary"
				:disabled="loading
					|| !searchQuery
					// If the search type is geboortedatum_achternaam, the geboortedatum is required
					|| (klantenSearchType === 'geboortedatum_achternaam' && !searchQuery_geboortedatum)"
				class="searchButton"
				@click="search">
				<template #icon>
					<Search :size="20" />
				</template>
				Zoeken
			</NcButton>
		</div>

		<div class="searchResultsContainer">
			<div v-if="klanten?.length && !loading">
				<NcListItem v-for="(klant, i) in klanten"
					:key="`${klant}${i}`"
					:name="`${getSex(klant)} ${getName(klant)} ${getSubname(klant)}`"
					:active="selectedKlant === klant?.id"
					:force-display-actions="true"
					:details="_.upperFirst(klant.type)"
					@click="setActive(klant.id)">
					<template #icon>
						<OfficeBuildingOutline v-if="klant.type === 'organisatie'"
							:class="selectedKlant === klant.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
						<AccountOutline v-if="klant.type === 'persoon'"
							:class="selectedKlant === klant.id && 'selectedZaakIcon'"
							disable-menu
							:size="44" />
					</template>
					<template #subname>
						{{ getSummary(klant) }}
					</template>
				</NcListItem>
			</div>

			<div v-if="!klanten?.length && !loading">
				Geen {{ startingType === 'persoon' ? 'personen' : 'organisaties' }} gevonden.
			</div>

			<NcLoadingIcon v-if="loading"
				class="loadingIcon"
				:size="64"
				appearance="dark"
				name="Aan het zoeken" />
		</div>

		<template #actions>
			<NcButton
				type="secondary"
				@click="closeModal()">
				<template #icon>
					<Cancel :size="20" />
				</template>
				Annuleer
			</NcButton>
			<NcButton
				type="primary"
				:disabled="!selectedKlant"
				@click="addKlant()">
				<template #icon>
					<Plus :size="20" />
				</template>
				Koppelen
			</NcButton>
		</template>
	</NcDialog>
</template>

<script>
// Components
import { NcButton, NcTextField, NcDialog, NcListItem, NcLoadingIcon, NcCheckboxRadioSwitch, NcDateTimePicker } from '@nextcloud/vue'
import { getTheme } from '../../services/getTheme.js'
import _ from 'lodash'

// Icons
import OfficeBuildingOutline from 'vue-material-design-icons/OfficeBuildingOutline.vue'
import AccountOutline from 'vue-material-design-icons/AccountOutline.vue'
import Plus from 'vue-material-design-icons/Plus.vue'
import Search from 'vue-material-design-icons/Magnify.vue'
import Cancel from 'vue-material-design-icons/Cancel.vue'
import getValidISOstring from '../../services/getValidISOstring.js'
export default {
	name: 'SearchKlantModal',
	components: {
		NcDialog,
		NcButton,
		NcListItem,
		OfficeBuildingOutline,
		AccountOutline,
		Search,
		NcCheckboxRadioSwitch,
		NcDateTimePicker,
		NcLoadingIcon,
	},
	props: {
		startingType: {
			type: String,
			required: false,
			default: 'all',
		},
	},
	data() {
		return {
			succes: false,
			loading: false,
			error: false,
			hasUpdated: false,
			klanten: [],
			searchQuery: '',
			searchQuery_geboortedatum: null,
			selectedKlant: null,
			klantenSearchType: 'emailadres',
		}
	},
	computed: {
		searchLabel() {
			const baseLabel = 'Zoek naar een '
			const typeLabels = {
				persoon: {
					default: 'persoon',
					geboortedatum_achternaam: ' met geboortedatum en achternaam',
					bsn: ' met BSN',
				},
				organisatie: {
					default: 'organisatie',
					bedrijfsnaam: ' met bedrijfsnaam',
					kvkNummer: ' met KVK nummer',
				},
			}
			const commonLabels = {
				postcode_huisnummer: ' met postcode en huisnummer',
				emailadres: ' met emailadres',
				telefoonnummer: ' met telefoonnummer',
			}

			let label = baseLabel + (typeLabels[this.startingType]?.default || '')
			label += typeLabels[this.startingType]?.[this.klantenSearchType] || ''
			label += commonLabels[this.klantenSearchType] || ''

			return label
		},
	},
	methods: {
		closeModalFromButton() {
			setTimeout(() => {
				this.closeModal()
			}, 300)
		},
		closeModal() {
			this.$emit('close-modal')

		},
		addKlant() {
			// eslint-disable-next-line no-console
			console.log('added')
			this.$emit('selected-klant', this.selectedKlant)
			this.closeModalFromButton()
		},

		search() {
			this.loading = true

			this.selectedKlant = null

			let queryParams = { [this.klantenSearchType]: this.searchQuery }
			const newQuery = this.searchQuery.trim()
			const splitQuery = newQuery.split(/ +/g)

			switch (this.klantenSearchType) {
			case 'postcode_huisnummer':
				queryParams = { postcode: splitQuery[0], huisnummer: splitQuery[1] }
				break
			case 'geboortedatum_achternaam':
				queryParams = {
					geboortedatum: this.searchQuery_geboortedatum && this.searchQuery_geboortedatum.toISOString() ? this.searchQuery_geboortedatum.toISOString() : '',
					achternaam: newQuery,
				}
				break
			case 'kvkNummer':
				queryParams = { kvkNummer: newQuery }
				break
			default:
				break
			}

			const searchParams = new URLSearchParams({
				...(this.searchQuery && queryParams),
				...(this.startingType && { type: this.startingType }),
			}).toString()

			klantStore.searchKlanten(searchParams)
				.then(() => {
					this.klanten = klantStore.klantenList
					this.loading = false
				})
		},
		getItemIcon() {
			const theme = getTheme()

			let appLocation = '/custom_apps'

			if (window.location.hostname === 'nextcloud.local') {
				appLocation = '/apps-extra'
			}

			return theme === 'light' ? `${appLocation}/zaakafhandelapp/img/office-building-outline-dark.svg` : `${appLocation}/zaakafhandelapp/img/office-building-outline.svg`
		},

		getName(klant) {
			if (klant.type === 'persoon') {
				return klant?.voornaam ?? 'onbekend'
			}
			if (klant.type === 'organisatie') {
				return klant?.bedrijfsnaam ?? 'onbekend'
			}
			return 'onbekend'
		},
		getSubname(klant) {
			if (klant.type === 'persoon') {
				return klant?.tussenvoegsel ? `${klant.tussenvoegsel} ${klant.achternaam}` : klant?.achternaam ? `${klant.achternaam}` : 'onbekend'
			}
			if (klant.type === 'organisatie') {
				return ''
			}
			return 'onbekend'
		},
		getSummary(klant) {
			if (klant.type === 'persoon') {
				const geboortedatum = getValidISOstring(klant.geboortedatum) ? new Date(klant.geboortedatum).toLocaleDateString() : 'onbekend'
				const geboortestad = klant.plaats ? `${klant.plaats}` : 'onbekend'
				return `${geboortedatum} - ${geboortestad}`
			}
			if (klant.type === 'organisatie') {
				return klant?.websiteUrl ?? 'onbekend'
			}
			return 'onbekend'
		},
		getSex(klant) {
			if (klant.type === 'persoon') {
				return `(${klant?.geslacht})`
			}
			return ''
		},
		setActive(klant) {
			if (this.selectedKlant === klant) {
				this.selectedKlant = null
			} else { this.selectedKlant = klant }
		},
	},
}
</script>

<style>

</style>

<style scoped>
.listContainer {
	margin-bottom: 10px;
}

.filtersContainer {
	display: ruby;
}

.searchContainer {
	display: flex;
	align-items: center;
	gap: 10px;
}
.searchField {
	width: auto;
}
.searchButton {
	margin-block-start: 3px;
	min-width: min-content !important;
}

.searchResultsContainer {
	border-top: 1px solid black;
	border-bottom: 1px solid black;
	padding-block: 20px;
	margin-block: 30px;
}

.flex {
	display: flex;
	gap: 10px;
}

.date-picker {
	margin-block-start: 3px;
}
</style>
