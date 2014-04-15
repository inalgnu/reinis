var url = casper.cli.get("url");

require("utils").dump(url);

casper.test.begin('Announcement list - Infinite scroll tests', 2, function(test) {
    casper.start(url).then(function() {
        this.echo('When we go to '+ url +'/ we want to see 10 announcements:');
        test.assertElementCount('.box', 10);
    });

    casper.then(function() {
        this.echo('Then we scroll down and we see 10 more announcements:')
        this.scrollTo(0, 2000);
        this.wait(1000, function() {
            test.assertElementCount('.box', 20);
        });
    });

    casper.run(function() {
        test.done();
    });
});

casper.run();
