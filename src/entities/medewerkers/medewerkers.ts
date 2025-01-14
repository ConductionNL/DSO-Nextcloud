import { SafeParseReturnType, z } from 'zod'
import { TMedewerker } from './medewerkers.types'

export class Medewerker implements TMedewerker {

	public id: string
	public voornaam: string
	public tussenvoegsel: string
	public achternaam: string
	public email: string
	public telefoonnummer: string

	constructor(source: TMedewerker) {
		this.id = source.id || ''
		this.voornaam = source.voornaam || ''
		this.tussenvoegsel = source.tussenvoegsel || ''
		this.achternaam = source.achternaam || ''
		this.email = source.email || ''
		this.telefoonnummer = source.telefoonnummer || ''
	}

	public validate(): SafeParseReturnType<TMedewerker, unknown> {
		const schema = z.object({
			id: z.string().optional(),
			voornaam: z.string().min(1),
			tussenvoegsel: z.string(),
			achternaam: z.string(),
			email: z.string().email(),
			telefoonnummer: z.string().min(1),
		})

		return schema.safeParse(this)
	}

}
