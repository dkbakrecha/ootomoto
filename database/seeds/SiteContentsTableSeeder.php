<?php

use Illuminate\Database\Seeder;

class SiteContentsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('site_contents')->insert([
            'unique_key' => 'ABOUTUS',
            'description' => 'Lorem ipsum dolor sit amet, no vis ferri menandri, ne lorem patrioque quaerendum cum. Duo integre moderatius eu. Eos saepe timeam persequeris at, no illum electram vituperatoribus sea, te reque harum insolens quo. Ei eum graeci nemore. Te mei blandit incorrupte, ius ut decore dicunt, vim ut decore labore.

Est dolorem suscipiantur id, ea animal offendit percipitur vis, has in cibo suavitate. Sed ea deserunt invenire, cum summo meliore et. Quo nemore nominati indoctum id. Malis oblique te usu, et causae splendide pri.

Sit utroque efficiendi ne, eu audiam debitis duo, eos ei labore comprehensam. Et feugiat molestie oportere mea. Putent consequat necessitatibus cu mei, no vix animal oblique mandamus. Quo no antiopam maluisset, iudico accusata appellantur id his.

Eam efficiantur consectetuer at, qui consul signiferumque mediocritatem an, est te utroque vulputate. Nulla debet legere eum at, quodsi delicata aliquando at his, has ut fierent accusamus principes. Eam ei utamur philosophia. No augue accusam usu, in ius facilisis evertitur.

Ad sit amet choro commodo. Vidit scriptorem te has, laoreet suscipit ius eu, mel option timeam assueverit ut. Hinc liber intellegam nec in, at quo unum assueverit. Labore delenit ocurreret vix eu, diam simul deserunt ut sea.',
            'link' => 'http://google.com',
        ]);
        
        DB::table('site_contents')->insert([
            'unique_key' => 'CANCELLATION',
            'description' => 'Lorem ipsum dolor sit amet, no vis ferri menandri, ne lorem patrioque quaerendum cum. Duo integre moderatius eu. Eos saepe timeam persequeris at, no illum electram vituperatoribus sea, te reque harum insolens quo. Ei eum graeci nemore. Te mei blandit incorrupte, ius ut decore dicunt, vim ut decore labore.

Est dolorem suscipiantur id, ea animal offendit percipitur vis, has in cibo suavitate. Sed ea deserunt invenire, cum summo meliore et. Quo nemore nominati indoctum id. Malis oblique te usu, et causae splendide pri.

Sit utroque efficiendi ne, eu audiam debitis duo, eos ei labore comprehensam. Et feugiat molestie oportere mea. Putent consequat necessitatibus cu mei, no vix animal oblique mandamus. Quo no antiopam maluisset, iudico accusata appellantur id his.

Eam efficiantur consectetuer at, qui consul signiferumque mediocritatem an, est te utroque vulputate. Nulla debet legere eum at, quodsi delicata aliquando at his, has ut fierent accusamus principes. Eam ei utamur philosophia. No augue accusam usu, in ius facilisis evertitur.

Ad sit amet choro commodo. Vidit scriptorem te has, laoreet suscipit ius eu, mel option timeam assueverit ut. Hinc liber intellegam nec in, at quo unum assueverit. Labore delenit ocurreret vix eu, diam simul deserunt ut sea.',
            'link' => '',
        ]);
    }
}
