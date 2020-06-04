<?php namespace Iivanov2\TranslationManager;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;
use Iivanov2\TranslationManager\Models\Translation;
use Illuminate\Support\Collection;

class Controller extends BaseController
{
    /** @var \Iivanov2\TranslationManager\Manager  */
    protected $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
    }

    public function showDashboard()
    {
        $locales = $this->manager->getLocales();

        $groups = Translation::groupBy('group');
        $excludedGroups = $this->manager->getConfig('exclude_groups');
        if($excludedGroups){
            $groups->whereNotIn('group', $excludedGroups);
        }
        $groups = $groups->select('group')->orderBy('group')->get()->pluck('group', 'group');
        if ($groups instanceof Collection) {
            $groups = $groups->all();
        }

        return view('translation-manager::dashboard')
                ->with('locales', $locales)
                ->with('groups', $groups);
    }

    public function postImport(Request $request)
    {
        $replace = $request->get('import_or_update') == 'update' ? true : false;
        $counter = $this->manager->importTranslations($replace);

        return ['status' => 'ok', 'counter' => $counter];
    }
    
    // Scans all files for strings within translation functions like trans(), transe_choice, etc.
    public function postFind() 
    {
        $numFound = $this->manager->findTranslations();

        return ['status' => 'ok', 'counter' => (int) $numFound];
    }

    // Exports all new translations in lang files or exports all new translations for particular group
    public function postPublish($group = null)
    {
         $json = false;

        if($group === '_json'){
            $json = true;
        }

        $this->manager->exportTranslations($group, $json);

        return ['status' => 'ok'];
    }

    public function postTranslate()
    {
        //TODO ACL permission to edit the resource

        $id = request()->get('pk'); //Primery Key. data-pk in the html. data-pk=0 when insert new translation; data-pk={id} when update existing translation
        $group = request()->get('group'); // data-group in the html.
        $locale = request()->get('locale'); // data-locale in the html.
        $key = request()->get('key'); // data-key in the html.
        $value = request()->get('value'); 

        if($id>0) { // update existing translation
            $translation = Translation::find($id); 
        }
        else { // insert new translation
            $translation = new Translation;
            $translation->locale=$locale;
            $translation->group=$group;
            $translation->key=$key;
        }

        $translation->value = (string) $value ?: null;
        $translation->status = Translation::STATUS_CHANGED;
        $translation->save();
        return ['status' => 'ok'];
    }
    
    /**
     * postSearchString - search for keys/translations in ltm_translations table by %string% and list all matches
     *
     * @param  mixed $request
     * @return void
     */
    public function postSearchString(Request $request)
    {
        $allStrings = Translation::where('key', 'LIKE', '%'.$request->string_to_find.'%')
            ->orWhere('value', 'LIKE', '%'.$request->string_to_find.'%')
            ->orderBy('id', 'desc')->get();

        $numChanged=0;
        $translationsBundle = [];
        foreach($allStrings as $translation) {
            $translationsBundle[$translation->key][$translation->locale] = $translation;

            if($translation->status==Translation::STATUS_CHANGED) {
                $numChanged++;
            }
        }

        $locales = $this->manager->getLocales();
        $numTranslations = count($translationsBundle);

        return view('translation-manager::translation_table')
            ->with('translationsBundle', $translationsBundle)
            ->with('locales', $locales)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged);
    }
    
    /**
     * postListGroup - list all key-translations from particular group
     */
    public function postListGroup(Request $request)
    {
        $group=$request->group;
        $allStrings = Translation::where('group', '=', $group)
            ->orderBy('id', 'desc')->get();

        $numChanged=0;
        $translationsBundle = [];
        foreach($allStrings as $translation) {
            $translationsBundle[$translation->key][$translation->locale] = $translation;

            if($translation->status==Translation::STATUS_CHANGED) {
                $numChanged++;
            }
        }

        $locales = $this->manager->getLocales();
        $numTranslations = count($translationsBundle);

        return view('translation-manager::translation_table')
            ->with('translationsBundle', $translationsBundle)
            ->with('locales', $locales)
            ->with('numTranslations', $numTranslations)
            ->with('numChanged', $numChanged);
    }
}
