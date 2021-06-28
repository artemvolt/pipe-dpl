/**
 * Используется для rules. Говорит нам является ли выбранная страна не часть России. По сути всегда только РФ может тут
 * быть false, но мб когда-то какая-то страна станет часть России.
 * @param {array} homelandCountries
 * @param {string} value
 * @constructor
 */
function isForeigner(homelandCountries, value) {
	if ('' === value) {
		return false;
	}
	return !homelandCountries.includes(parseInt(value));
}