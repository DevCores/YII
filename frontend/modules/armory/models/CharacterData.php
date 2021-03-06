<?php

namespace frontend\modules\armory\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;

use common\models\chars\Characters;
use common\models\chars\CharacterAchievement;
use common\models\chars\CharacterTalent;

use common\models\armory\ArmoryProfessions;
use common\models\armory\ArmoryTalentTab;
use common\models\armory\ArmoryTalent;

/**
 * CharacterData model
 */
class CharacterData extends Model
{
 
    const EMPTY_DATA = '- - - -';
    private $name;
    public $talents = [];
    
    public function __construct($character) {
        $this->name = $character;
        $this->talents = [
            'name' => $character,
        ];
        return $this;
    }
    
    public function generateGeneral() {
        $data = Yii::$app->cache->get(Yii::$app->request->url);
        if($data === false) {
            
            $character = Characters::find()->where(['name' => $this->name])->with([
                'relationStats',
                'relationTitle',
                'relationEquipment',
                'relationGeneralProfessions',
                'relationGuild.relationGuild'
            ])->one();
            
            $data['name'] = $this->name;
            $data['level'] = $character->level;
            $data['race_index'] = $character->race;
            $data['race'] = Yii::$app->AppHelper->getRaces($character->race);
            $data['class_index'] = $character->class;
            $data['class'] = Yii::$app->AppHelper->getClasses($character->class);
            $data['title'] = CharacterData::EMPTY_DATA;
            
            if($character->relationTitle) {
                $gender = $character->gender ? 'F' : 'M';
                $data['title'] = Yii::$app->AppHelper->i18nAttribute($character->relationTitle,'title_' . $gender);
            }
            
            $data['guild'] = $character->relationGuild ? $character->relationGuild->relationGuild->name : CharacterData::EMPTY_DATA;
            $data['stats']['maxhealth'] = $character->relationStats ? $character->relationStats->maxhealth : 0;
            $data['stats']['maxpower'] = Yii::$app->AppHelper->getCharacterPowerByClass($character->class);
            $data['stats']['strength'] = $character->relationStats ? $character->relationStats->strength : 0;
            $data['stats']['agility'] = $character->relationStats ? $character->relationStats->agility : 0;
            $data['stats']['stamina'] = $character->relationStats ? $character->relationStats->stamina : 0;
            $data['stats']['intellect'] = $character->relationStats ? $character->relationStats->intellect : 0;
            $data['stats']['spirit'] = $character->relationStats ? $character->relationStats->spirit : 0;
            $data['stats']['armor'] = $character->relationStats ? $character->relationStats->armor : 0;
            $data['stats']['expertiseBaseAttackPct'] = $character->relationStats ? $character->relationStats->expertiseBaseAttackPct : 0;
            $data['stats']['attackPower'] = $character->relationStats ? $character->relationStats->attackPower : 0;
            $data['stats']['hitMelee'] = $character->relationStats ? $character->relationStats->hitMelee : 0;
            $data['stats']['critPct'] = $character->relationStats ? $character->relationStats->critPct : 0;
            $data['stats']['expertiseOffAttackPct'] = $character->relationStats ? $character->relationStats->expertiseOffAttackPct : 0;
            $data['stats']['rangedAttackPower'] = $character->relationStats ? $character->relationStats->rangedAttackPower : 0;
            $data['stats']['hitRanged'] = $character->relationStats ? $character->relationStats->hitRanged : 0;
            $data['stats']['rangedCritPct'] = $character->relationStats ? $character->relationStats->rangedCritPct : 0;
            $data['stats']['spellPower'] = $character->relationStats ? $character->relationStats->spellPower : 0;
            $data['stats']['hitSpell'] = $character->relationStats ? $character->relationStats->hitSpell : 0;
            $data['stats']['hasteSpell'] = $character->relationStats ? $character->relationStats->hasteSpell : 0;
            $data['stats']['dodgePct'] = $character->relationStats ? $character->relationStats->dodgePct : 0;
            $data['stats']['parryPct'] = $character->relationStats ? $character->relationStats->parryPct : 0;
            $data['stats']['blockPct'] = $character->relationStats ? $character->relationStats->blockPct : 0;
            $data['stats']['resilience'] = $character->relationStats ? $character->relationStats->resilience : 0;
            
            //todo
            $data['stats']['spellCrit'] = 0;
            $data['stats']['melee_damage_from'] = 0;
            $data['stats']['melee_damage_to'] = 0;
            $data['stats']['range_damage_from'] = 0;
            $data['stats']['range_damage_to'] = 0;
            $data['stats']['experience'] = 0;
            $data['stats']['spellHealing'] = 0;
            $data['stats']['mp5'] = 0;
            $data['stats']['defence'] = 0;
            
            $data['items'] = [];
            $_items = $character->relationEquipment;
            foreach(Yii::$app->AppHelper::$ARRAY_SLOTS as $slot) {
                $item = current($_items);
                if(isset($item)) {
                    if(is_object($item) && $slot == $item->slot && isset($item->relationItemInstance) && isset($item->relationItemInstance->relationArmoryItem)) {
                        $data['items'][$slot] = [
                            'item_url' => Yii::$app->AppHelper->buildDBUrl($item->relationItemInstance->itemEntry, Yii::$app->AppHelper::$TYPE_ITEM),
                            'icon_url' => Yii::$app->AppHelper->buildItemIconUrl($slot, $item->relationItemInstance->relationArmoryItem->relationIcon->icon),
                            'rel_item' => Yii::$app->AppHelper->buildItemRel($item->relationItemInstance->itemEntry,explode(' ',$item->relationItemInstance->enchantments)),
                        ];
                    } else {
                        $data['items'][$slot] = [
                            'item_url' => '#self',
                            'icon_url' => Yii::$app->AppHelper->buildItemIconUrl($slot),
                            'rel_item' => '',
                        ];
                    }
                }
                next($_items);
            }
            $data['achievements'] = [];
            $achiv_models = CharacterAchievement::find()->where(['guid' => $character->guid])->limit(10)->orderBy(['date' => SORT_DESC])->with('relationAchievement')->asArray()->all();
            foreach($achiv_models as $ach_m) {
                $data['achievements'][] = [
                    'id' => $ach_m['achievement'],
                    'name' => $ach_m['relationAchievement'] ? Yii::$app->AppHelper->i18nAttribute($ach_m['relationAchievement'],'name') : 'null',
                    'icon' => $ach_m['relationAchievement'] ? $ach_m['relationAchievement']['iconname'] : 'null.png',
                    'date' => $ach_m['date'],
                ];
            }
            $data['professions'] = [];
            if($character->relationGeneralProfessions) {
                foreach($character->relationGeneralProfessions as $prof) {
                    
                }
            }
            Yii::$app->cache->set(Yii::$app->request->url,$data,Yii::$app->keyStorage->get('frontend.cache_armory_character'));
        }
        return $data;
    }
    
    public function generateTalents() {
        $data = Yii::$app->cache->get(Yii::$app->request->url);
        if($data === false) {
            $character = Characters::find()->where(['name' => $this->name])->one();
            $data['name'] = $character->name;
            $talentTabs = ArmoryTalentTab::find()
                    ->where([
                        'refmask_chrclasses' => Yii::$app->AppHelper->getArmoryClassMask($character->class)
                    ])->with('relationTree.relationSpell.relationIcon')->asArray()->all();
            for($i = 0; $i <= 1; $i++) {
                foreach($talentTabs as $tab) {
                    $tab_counter = 0;
                    $data['talents'][$i][$tab['tab_number']] = [
                        'name' => Yii::$app->AppHelper->i18nAttribute($tab, 'name'),
                        'counter' => $tab_counter,
                    ];
                    $tree = ArrayHelper::index($tab['relationTree'], null, 'Row');
                    foreach($tree as $k_row => $row) {
                        foreach($row as $k_col => $col) {
                            $talent_learned = static::checkCountTalentLearned($character->guid, $col, $i);
                            $tab_counter += $talent_learned;
                            $data['talents'][$i][$tab['tab_number']]['tree'][$k_row][$col['Col']]['id_spell'] = $col['Rank_1'];
                            $data['talents'][$i][$tab['tab_number']]['tree'][$k_row][$col['Col']]['max'] = Yii::$app->AppHelper->getMaxRankSpell($col);
                            $data['talents'][$i][$tab['tab_number']]['tree'][$k_row][$col['Col']]['count'] = $talent_learned;
                            $spell_icon_array = explode('\\',$col['relationSpell']['relationIcon']['Name']);
                            $data['talents'][$i][$tab['tab_number']]['tree'][$k_row][$col['Col']]['icon_name'] = strtolower(end($spell_icon_array));
                        }
                    }
                    $data['talents'][$i][$tab['tab_number']]['counter'] = $tab_counter;
                }
            }
            Yii::$app->cache->set(Yii::$app->request->url,$data,Yii::$app->keyStorage->get('frontend.cache_armory_character_talents'));
        }
        return $data;
    }
    
    public static function checkCountTalentLearned($char_guid, $col, $group) {
        $spells = CharacterTalent::find()->where([
            'guid' => $char_guid,
            'spell' => [
                $col['Rank_5'],  
                $col['Rank_4'],  
                $col['Rank_3'],  
                $col['Rank_2'],  
                $col['Rank_1'],  
            ],
            'talentGroup' => $group
            ])->asArray()->all();
        if(!$spells) {
            $output = 0;
        } else {
            foreach($spells as $spell) {
                if($col['Rank_5'] == $spell['spell']) {
                    $output = 5;
                } elseif ($col['Rank_4'] == $spell['spell']) {
                    $output = 4;
                } elseif ($col['Rank_3'] == $spell['spell']) {
                    $output = 3;
                } elseif ($col['Rank_2'] == $spell['spell']) {
                    $output = 2;
                } elseif ($col['Rank_1'] == $spell['spell']) {
                    $output = 1;
                } else {
                    $output = 0;
                }
            }
        }
        return $output;
    }   
}