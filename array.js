Array.prototype.sum = function () {
    return this.reduce(function(previousValue, currentValue) {
        return previousValue + currentValue;
    });
};

Array.prototype.mean = function () {
    var sum = this.reduce(function(previousValue, currentValue) {
        return previousValue + currentValue;
    });

    return sum / this.length;
};

Array.prototype.min = function () {
    return this.reduce(function(previousValue, currentValue) {
        return previousValue < currentValue ? previousValue : currentValue;
    });
};

Array.prototype.max = function () {
    return this.reduce(function(previousValue, currentValue) {
        return previousValue > currentValue ? previousValue : currentValue;
    });
};

Array.prototype.deviation = function () {
    var diff = [],
        mean = this.mean();

    for(var i = 0;i<this.length;i++) {
        diff.push(Math.pow(this[i] - mean, 2));
    }

    return Math.sqrt(diff.mean());
};
