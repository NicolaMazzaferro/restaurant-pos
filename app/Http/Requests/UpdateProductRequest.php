<?php
/**
 * UpdateProductRequest
 *
 * Scopo:
 * - Validare l'aggiornamento di un Product.
 * - Consente aggiornamenti parziali (regola `sometimes`).
 * - Impone che sia presente **almeno un campo** tra name, category_id, price, stock
 *   usando la combinazione `required_without_all`.
 *
 * Note:
 * - Usiamo 'bail' per fermare la validazione alla prima violazione per ogni campo.
 * - La logica di autorizzazione è demandata a Policies/Gates (qui ritorna true per l’MVP).
 */

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    /**
     * In MVP autorizziamo sempre; in produzione usa Policy (es. can:update, Product).
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Regole:
     * - `sometimes`: valida solo se il campo è presente nella request
     * - `required_without_all`: garantisce che **almeno uno** dei campi sia inviato
     */
    public function rules(): array
    {
        return [
            'name' => [
                'bail', 'sometimes', 'string', 'max:255',
                // se gli altri mancano, questo diventa required
                'required_without_all:category_id,price,stock',
            ],
            'category_id' => [
                'bail', 'sometimes', 'nullable', 'exists:categories,id',
                'required_without_all:name,price,stock',
            ],
            'price' => [
                'bail', 'sometimes', 'numeric', 'min:0',
                'required_without_all:name,category_id,stock',
            ],
            'stock' => [
                'bail', 'sometimes', 'integer', 'min:0',
                'required_without_all:name,category_id,price',
            ],
        ];
    }

    /**
     * Messaggi personalizzati (più chiari per l'utente API).
     */
    public function messages(): array
    {
        $atLeastOne = 'Devi fornire almeno un campo tra name, category_id, price, stock.';
        return [
            'name.required_without_all'        => $atLeastOne,
            'category_id.required_without_all' => $atLeastOne,
            'price.required_without_all'       => $atLeastOne,
            'stock.required_without_all'       => $atLeastOne,
        ];
    }

    /**
     * Nomi "umani" opzionali, utili nei messaggi di errore.
     */
    public function attributes(): array
    {
        return [
            'name'        => 'nome',
            'category_id' => 'categoria',
            'price'       => 'prezzo',
            'stock'       => 'stock',
        ];
    }

    /**
     * (Opzionale) Normalizza numeri se arrivano come stringhe con spazi/virgole.
     * Non indispensabile per l’MVP, ma utile se il client invia "6,50".
     */
    protected function prepareForValidation(): void
    {
        if ($this->has('price')) {
            // sostituisci eventuale virgola decimale con punto
            $normalized = str_replace([' ', ','], ['', '.'], (string) $this->input('price'));
            $this->merge(['price' => $normalized]);
        }
    }
}
